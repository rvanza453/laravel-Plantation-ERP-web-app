<?php

namespace Modules\ServiceAgreementSystem\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\ServiceAgreementSystem\Models\Job;
use Modules\ServiceAgreementSystem\Models\UspkBapp;
use Modules\ServiceAgreementSystem\Models\UspkSubmission;

class UspkBappController extends Controller
{
    public function index()
    {
        $bapps = UspkBapp::with(['job', 'contractor', 'uploader'])
            ->withCount('submissions')
            ->latest()
            ->paginate(15);

        return view('serviceagreementsystem::bapp.index', compact('bapps'));
    }

    public function create()
    {
        $jobs = Job::orderBy('name')->get();

        return view('serviceagreementsystem::bapp.create', compact('jobs'));
    }

    public function getEligibleUspks(Request $request)
    {
        $jobId = $request->query('job_id');

        if (!$jobId) {
            return response()->json([]);
        }

        // BAPP dapat mencakup multi blok dan multi kontraktor,
        // namun semua USPK wajib berada pada pekerjaan (job) yang sama.
        $uspks = UspkSubmission::with(['department', 'selectedTender.contractor'])
            ->where('job_id', $jobId)
            ->whereNull('uspk_bapp_id')
            ->whereHas('selectedTender')
            ->whereNotNull('work_reported_completed_at')
            ->get();

        return response()->json($uspks->map(function($uspk) {
            $selectedTender = $uspk->selectedTender;

            return [
                'id' => $uspk->id,
                'uspk_number' => $uspk->uspk_number,
                'title' => $uspk->title,
                'department_name' => $uspk->department->name ?? '-',
                'completion_date' => $uspk->work_reported_completed_at ? $uspk->work_reported_completed_at->format('d M Y') : '-',
                'contractor_name' => $selectedTender?->contractor?->name ?? '-',
                'tender_value' => $selectedTender ? 'Rp ' . number_format((float) $selectedTender->tender_value, 0, ',', '.') : '-'
            ];
        }));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bapp_number' => 'required|string|unique:uspk_bapps,bapp_number',
            'bapp_date' => 'required|date',
            'job_id' => 'required|exists:jobs,id',
            'document_link' => 'required|url',
            'uspk_ids' => 'required|array|min:1',
            'uspk_ids.*' => 'exists:uspk_submissions,id',
        ]);

        DB::beginTransaction();
        try {
            $selectedUspks = UspkSubmission::query()
                ->with('selectedTender')
                ->whereIn('id', $validated['uspk_ids'])
                ->lockForUpdate()
                ->get();

            if ($selectedUspks->count() !== count($validated['uspk_ids'])) {
                throw new \RuntimeException('Sebagian USPK yang dipilih tidak ditemukan. Silakan muat ulang halaman.');
            }

            $invalidUspk = $selectedUspks->first(function (UspkSubmission $uspk) use ($validated) {
                return (int) $uspk->job_id !== (int) $validated['job_id']
                    || !is_null($uspk->uspk_bapp_id)
                    || is_null($uspk->work_reported_completed_at)
                    || !$uspk->selectedTender;
            });

            if ($invalidUspk) {
                throw new \RuntimeException('Daftar USPK tidak valid. Pastikan semua USPK satu pekerjaan yang sama, sudah selesai, memiliki pemenang tender, dan belum masuk BAPP lain.');
            }

            $contractorIds = $selectedUspks
                ->pluck('selectedTender.contractor_id')
                ->filter()
                ->unique()
                ->values();

            $singleContractorId = $contractorIds->count() === 1
                ? (int) $contractorIds->first()
                : null;

            // Create BAPP
            $bapp = UspkBapp::create([
                'bapp_number' => $validated['bapp_number'],
                'bapp_date' => $validated['bapp_date'],
                'job_id' => $validated['job_id'],
                'contractor_id' => $singleContractorId,
                'document_link' => $validated['document_link'],
                'uploaded_by' => auth()->id(),
            ]);

            // Assign BAPP to selected USPKs and update status
            UspkSubmission::whereIn('id', $validated['uspk_ids'])
                ->update([
                    'uspk_bapp_id' => $bapp->id,
                    'status' => UspkSubmission::STATUS_COMPLETED
                ]);

            DB::commit();

            return redirect()->route('sas.bapp.index')->with('success', 'BAPP berhasil dibuat dan USPK terkait berstatus Selesai.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat BAPP: ' . $e->getMessage())->withInput();
        }
    }

    public function show(UspkBapp $bapp)
    {
        $bapp->load(['job', 'contractor', 'uploader', 'submissions.department']);
        
        return view('serviceagreementsystem::bapp.show', compact('bapp'));
    }
}
