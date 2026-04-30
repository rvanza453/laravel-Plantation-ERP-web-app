<?php

namespace Modules\LabSystem\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\LabSystem\Models\LabVerifierAssignment;
use App\Models\User;
use Modules\SystemISPO\Models\Site;
use DB;

class LabVerifierConfigController extends Controller
{
    /**
     * Display list of all verifier assignments (config page)
     */
    public function index(Request $request)
    {
        // Only admin/supervisor can manage config
        abort_unless(auth()->user()?->hasRole(['admin', 'supervisor', 'manager']), 403);

        $siteId = $request->input('site_id');
        $type = $request->input('type'); // parameter, category, shift, global
        
        $query = LabVerifierAssignment::with('user', 'site');

        if ($siteId) {
            $query->where(fn($q) => $q->where('site_id', $siteId)->orWhereNull('site_id'));
        }

        if ($type) {
            $query->where('assignment_type', $type);
        }

        $assignments = $query->orderBy('assignment_type')->orderBy('assignment_value')->paginate(15);
        $sites = Site::orderBy('name')->get();
        $verifiers = User::with('roles')->orderBy('name')->get();

        return view('labsystem::config.verifier-assignments', compact(
            'assignments',
            'sites',
            'verifiers',
            'siteId',
            'type'
        ));
    }

    /**
     * Show form to create new assignment
     */
    public function create()
    {
        abort_unless(auth()->user()?->hasRole(['admin', 'supervisor']), 403);

        $sites = Site::orderBy('name')->get();
        $verifiers = User::with('roles')->orderBy('name')->get();

        // Get available parameters from database
        $parameters = DB::table('lab_sampling_parameters')->distinct()->pluck('parameter_name');
        $categories = DB::table('lab_sampling_parameters')->distinct()->pluck('category');

        return view('labsystem::config.verifier-assignment-form', compact(
            'sites',
            'verifiers',
            'parameters',
            'categories'
        ));
    }

    /**
     * Store new assignment
     */
    public function store(Request $request)
    {
        abort_unless(auth()->user()?->hasRole(['admin', 'supervisor']), 403);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'site_id' => 'nullable|exists:sites,id',
            'assignment_type' => 'required|in:parameter,category,shift,global',
            'assignment_value' => 'required_unless:assignment_type,global|nullable|string',
            'notes' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        // Prevent duplicate assignments
        $existingQuery = LabVerifierAssignment::where('user_id', $validated['user_id'])
            ->where('assignment_type', $validated['assignment_type']);

        if ($validated['assignment_type'] !== 'global') {
            $existingQuery->where('assignment_value', $validated['assignment_value'] ?? null);
        }

        if ($validated['site_id']) {
            $existingQuery->where('site_id', $validated['site_id']);
        } else {
            $existingQuery->whereNull('site_id');
        }

        if ($existingQuery->exists()) {
            return back()->withErrors(['error' => 'Penugasan ini sudah ada']);
        }

        LabVerifierAssignment::create($validated);

        return redirect('lab/config/verifiers')->with('success', 'Penugasan verifier berhasil ditambahkan');
    }

    /**
     * Show form to edit assignment
     */
    public function edit(LabVerifierAssignment $assignment)
    {
        abort_unless(auth()->user()?->hasRole(['admin', 'supervisor']), 403);

        $sites = Site::orderBy('name')->get();
        $verifiers = User::with('roles')->orderBy('name')->get();
        $parameters = DB::table('lab_sampling_parameters')->distinct()->pluck('parameter_name');
        $categories = DB::table('lab_sampling_parameters')->distinct()->pluck('category');

        return view('labsystem::config.verifier-assignment-form', compact(
            'assignment',
            'sites',
            'verifiers',
            'parameters',
            'categories'
        ));
    }

    /**
     * Update assignment
     */
    public function update(Request $request, LabVerifierAssignment $assignment)
    {
        abort_unless(auth()->user()?->hasRole(['admin', 'supervisor']), 403);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'site_id' => 'nullable|exists:sites,id',
            'assignment_type' => 'required|in:parameter,category,shift,global',
            'assignment_value' => 'required_unless:assignment_type,global|nullable|string',
            'notes' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $assignment->update($validated);

        return redirect('lab/config/verifiers')->with('success', 'Penugasan verifier berhasil diperbarui');
    }

    /**
     * Delete assignment
     */
    public function destroy(LabVerifierAssignment $assignment)
    {
        abort_unless(auth()->user()?->hasRole(['admin', 'supervisor']), 403);

        $assignment->delete();

        return back()->with('success', 'Penugasan verifier berhasil dihapus');
    }

    /**
     * Get verifiers assigned to specific batch for inline verification
     */
    public function getAssignedVerifiers(Request $request)
    {
        $batchId = $request->input('batch_id');
        $batch = DB::table('lab_sampling_batches')->where('id', $batchId)->first();

        if (!$batch) {
            return response()->json(['error' => 'Batch tidak ditemukan'], 404);
        }

        // Get all measurements for this batch to identify categories and parameters
        $measurements = DB::table('lab_sampling_measurements as m')
            ->join('lab_sampling_parameters as p', 'p.id', '=', 'm.lab_sampling_parameter_id')
            ->where('m.lab_sampling_batch_id', $batchId)
            ->select(['p.parameter_name', 'p.category'])
            ->distinct()
            ->get();

        // Get assigned verifiers (global + category-specific + parameter-specific)
        $verifierIds = [];
        foreach ($measurements as $m) {
            $verifiers = LabVerifierAssignment::getVerifiersFor(
                $m->parameter_name,
                $m->category,
                $batch->site_id ?? null
            );

            foreach ($verifiers as $v) {
                $verifierIds[$v->user_id] = $v->user;
            }
        }

        // If no specific assigned, show all QC/admin users
        if (empty($verifierIds)) {
            $verifierIds = User::role(['quality control', 'admin', 'supervisor'])
                ->pluck('id')
                ->mapWithKeys(fn($id) => [$id => User::find($id)])
                ->toArray();
        }

        return response()->json(['verifiers' => array_values($verifierIds)]);
    }
}
