<?php

namespace Modules\ServiceAgreementSystem\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Modules\ServiceAgreementSystem\Http\Requests\StoreUspkSubmissionRequest;
use Modules\ServiceAgreementSystem\Models\UspkApproval;
use Modules\ServiceAgreementSystem\Models\UspkBlockProgress;
use Modules\ServiceAgreementSystem\Models\UspkSubmission;
use Modules\ServiceAgreementSystem\Services\ContractorService;
use Modules\ServiceAgreementSystem\Services\UspkSubmissionService;
use Modules\ServiceAgreementSystem\Models\Department;
use Modules\ServiceAgreementSystem\Models\SubDepartment;
use Modules\ServiceAgreementSystem\Models\Block;
use Modules\ServiceAgreementSystem\Models\Job;
use Modules\ServiceAgreementSystem\Models\UspkBudgetActivity;

class UspkSubmissionController extends Controller
{
    public function __construct(
        protected UspkSubmissionService $uspkService,
        protected ContractorService $contractorService
    ) {}

    public function index(Request $request)
    {
        $status = $request->get('status');
        $userId = $this->shouldScopeToSubmitter() ? auth()->id() : null;
        $submissions = $this->uspkService->getAll($status, $userId);

        $authUserId = (int) auth()->id();
        $canonicalRole = $this->getCanonicalSasRole();
        $isSasAdmin = $this->isSasAdmin();
        $today = Carbon::today();
        $soonDeadline = Carbon::today()->addDays(3);

        $scopeForStaff = function ($query) use ($canonicalRole, $authUserId) {
            if ($canonicalRole === 'staff') {
                $query->where('submitted_by', $authUserId);
            }
        };

        $approvalActionItems = UspkSubmission::query()
            ->with(['department'])
            ->whereIn('status', [UspkSubmission::STATUS_SUBMITTED, UspkSubmission::STATUS_IN_REVIEW])
            ->whereHas('approvals', function ($query) use ($authUserId, $isSasAdmin) {
                $query->where('status', UspkApproval::STATUS_PENDING);

                if (!$isSasAdmin) {
                    $query->where('user_id', $authUserId);
                }
            })
            ->when(!$isSasAdmin && $canonicalRole !== 'approver', $scopeForStaff)
            ->latest()
            ->limit(6)
            ->get();

        $hasLegalSpkPathColumn = Schema::hasColumn('uspk_submissions', 'legal_spk_document_path');
        $hasSubmitterSignedSpkColumn = Schema::hasColumn('uspk_submissions', 'submitter_signed_spk_document_path');
        $hasBlockProgressTable = Schema::hasTable('uspk_block_progresses');

        $legalPublishItems = $hasLegalSpkPathColumn
            ? UspkSubmission::query()
                ->with(['department'])
                ->where('status', UspkSubmission::STATUS_APPROVED)
                ->whereNull('legal_spk_document_path')
                ->when($canonicalRole === 'staff', $scopeForStaff)
                ->latest()
                ->limit(6)
                ->get()
            : collect();

        $deadlineAlertItems = ($hasSubmitterSignedSpkColumn && $hasBlockProgressTable)
            ? UspkSubmission::query()
                ->with(['department', 'blockProgresses.block'])
                ->whereNotNull('submitter_signed_spk_document_path')
                ->whereHas('blockProgresses', function ($query) use ($soonDeadline) {
                    $query->where('status', UspkBlockProgress::STATUS_PENDING)
                        ->whereNotNull('deadline_at')
                        ->whereDate('deadline_at', '<=', $soonDeadline);
                })
                ->when($canonicalRole === 'staff', $scopeForStaff)
                ->latest()
                ->limit(6)
                ->get()
                ->map(function (UspkSubmission $submission) use ($today) {
                    $pendingWithDeadline = $submission->blockProgresses
                        ->filter(function ($progress) {
                            return (string) $progress->status === UspkBlockProgress::STATUS_PENDING
                                && !empty($progress->deadline_at);
                        })
                        ->sortBy('deadline_at')
                        ->values();

                    $nearest = $pendingWithDeadline->first();
                    $overdueCount = $pendingWithDeadline->filter(fn ($progress) => $progress->deadline_at->lt($today))->count();

                    return [
                        'submission' => $submission,
                        'nearest_deadline' => optional($nearest)->deadline_at,
                        'overdue_count' => $overdueCount,
                        'due_soon_count' => $pendingWithDeadline->count() - $overdueCount,
                    ];
                })
            : collect();

        $deadlineSetupItems = ($hasSubmitterSignedSpkColumn && $hasBlockProgressTable)
            ? UspkSubmission::query()
                ->with(['department', 'blockProgresses'])
                ->whereNotNull('submitter_signed_spk_document_path')
                ->whereHas('blockProgresses', function ($query) {
                    $query->whereNull('deadline_at');
                })
                ->when($canonicalRole === 'staff', $scopeForStaff)
                ->latest()
                ->limit(6)
                ->get()
            : collect();

        $reminders = [
            'approvalActionItems' => $approvalActionItems,
            'legalPublishItems' => $legalPublishItems,
            'deadlineAlertItems' => $deadlineAlertItems,
            'deadlineSetupItems' => $deadlineSetupItems,
            'canManageDeadline' => in_array($canonicalRole, ['qc', 'admin'], true) || $isSasAdmin,
        ];

        return view('serviceagreementsystem::uspk.index', compact('submissions', 'status', 'reminders'));
    }

    public function create()
    {
        $departments = Department::with('site')->orderBy('name')->get(['id', 'site_id', 'name']);
        $contractors = $this->contractorService->getActive();
        $jobs = Job::orderBy('name')->get(['id', 'site_id', 'code', 'name']);

        return view('serviceagreementsystem::uspk.create', compact('departments', 'contractors', 'jobs'));
    }

    public function store(StoreUspkSubmissionRequest $request)
    {
        $data = $request->except('tenders');
        $tenders = $this->processTenders($request);

        $submission = $this->uspkService->store($data, $tenders);

        $saveDraft = $request->boolean('save_draft');

        if ($saveDraft) {
            return redirect()->route('sas.uspk.index')->with('success', 'USPK berhasil dibuat sebagai draft.');
        }

        try {
            $this->uspkService->submit($submission);
        } catch (\Throwable $e) {
            return redirect()->route('sas.uspk.show', $submission)
                ->with('error', 'USPK tersimpan sebagai draft, tetapi gagal disubmit: ' . $e->getMessage());
        }

        return redirect()->route('sas.uspk.show', $submission)->with('success', 'USPK berhasil dibuat dan langsung disubmit untuk proses review.');

    }

    public function show(UspkSubmission $uspk)
    {
        if ($this->shouldScopeToSubmitter() && (int) $uspk->submitted_by !== (int) auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke data USPK ini.');
        }

        $this->uspkService->reconcileWinnerFromFinalApproval($uspk);

        $uspk = $this->uspkService->findById($uspk->id);
        return view('serviceagreementsystem::uspk.show', compact('uspk'));
    }

    public function edit(UspkSubmission $uspk)
    {
        $this->authorizeManageSubmission($uspk, 'edit');

        if (!$uspk->isEditable()) {
            return redirect()->route('sas.uspk.show', $uspk)->with('error', 'USPK tidak dapat diedit.');
        }

        $uspk->load(['tenders.contractor']);
        $departments = Department::with('site')->orderBy('name')->get(['id', 'site_id', 'name']);
        $contractors = $this->contractorService->getActive();
        $jobs = Job::orderBy('name')->get(['id', 'site_id', 'code', 'name']);
        $subDepartments = SubDepartment::where('department_id', $uspk->department_id)->get();
        $blocks = Block::where('sub_department_id', $uspk->sub_department_id)->get();

        return view('serviceagreementsystem::uspk.edit', compact('uspk', 'departments', 'contractors', 'jobs', 'subDepartments', 'blocks'));
    }

    public function update(StoreUspkSubmissionRequest $request, UspkSubmission $uspk)
    {
        $this->authorizeManageSubmission($uspk, 'update');

        if (!$uspk->isEditable()) {
            return redirect()->route('sas.uspk.show', $uspk)->with('error', 'USPK tidak dapat diedit.');
        }

        $data = $request->except('tenders');
        $tenders = $this->processTenders($request);

        $this->uspkService->update($uspk, $data, $tenders);

        return redirect()->route('sas.uspk.show', $uspk)->with('success', 'USPK berhasil diperbarui.');
    }

    public function destroy(UspkSubmission $uspk)
    {
        $this->authorizeManageSubmission($uspk, 'destroy');
        $this->uspkService->delete($uspk);
        return redirect()->route('sas.uspk.index')->with('success', 'USPK berhasil dihapus.');
    }

    /**
     * Submit USPK (draft → submitted)
     */
    public function submit(UspkSubmission $uspk)
    {
        $this->authorizeManageSubmission($uspk);

        try {
            $this->uspkService->submit($uspk);
            return redirect()->route('sas.uspk.show', $uspk)->with('success', 'USPK berhasil disubmit.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    protected function authorizeManageSubmission(UspkSubmission $uspk, string $action = 'manage'): void
    {
        if ($this->isSasAdmin()) {
            return;
        }

        if ((int) $uspk->submitted_by !== (int) auth()->id()) {
            abort(403, 'Anda tidak berwenang mengelola USPK ini.');
        }

        if (in_array($action, ['edit', 'update', 'destroy'], true) && $this->hasAnyApproverDecision($uspk)) {
            abort(403, 'USPK tidak dapat diubah atau dihapus karena sudah ada keputusan dari approver.');
        }
    }

    protected function shouldScopeToSubmitter(): bool
    {
        return $this->getCanonicalSasRole() === 'staff';
    }

    protected function isSasAdmin(): bool
    {
        $user = auth()->user();
        $role = $this->getCanonicalSasRole();

        return $role === 'admin' || $user?->hasAnyRole(['Admin', 'Super Admin']);
    }

    protected function hasAnyApproverDecision(UspkSubmission $uspk): bool
    {
        return $uspk->approvals()
            ->whereIn('status', ['approved', 'on_hold', 'rejected'])
            ->exists();
    }

    protected function getCanonicalSasRole(): string
    {
        $role = strtolower(trim((string) auth()->user()?->moduleRole('sas')));

        return match ($role) {
            'asisten afdeling', 'pengaju' => 'staff',
            'manager', 'ktu', 'gm' => 'approver',
            default => $role,
        };
    }

    /**
     * API: Get sub departments by department
     */
    public function getSubDepartments(int $departmentId)
    {
        $subDepartments = SubDepartment::where('department_id', $departmentId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($subDepartments);
    }

    /**
     * API: Get blocks by sub department
     */
    public function getBlocks(int $subDepartmentId)
    {
        $blocks = Block::where('sub_department_id', $subDepartmentId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        return response()->json($blocks);
    }

    /**
     * API: Get budget activities by afdeling + job + year
     */
    public function getBudgetActivities(Request $request)
    {
        $validated = $request->validate([
            'sub_department_id' => ['required', 'integer', 'exists:sub_departments,id'],
            'job_id' => ['nullable', 'integer', 'exists:jobs,id'],
            'year' => ['nullable', 'integer', 'between:2000,2100'],
        ]);

        $year = (int) ($validated['year'] ?? now()->year);

        $baseQuery = UspkBudgetActivity::query()
            ->where('sub_department_id', $validated['sub_department_id'])
            ->where('is_active', true)
            ->where('year', $year)
            ->with('job');

        $query = clone $baseQuery;

        if (!empty($validated['job_id'])) {
            $query->where('job_id', $validated['job_id']);
        }

        $budgets = $query->orderBy('job_id')->get();

        if ($budgets->isEmpty() && !empty($validated['job_id'])) {
            $budgets = $baseQuery
                ->orderByRaw('CASE WHEN job_id = ? THEN 0 ELSE 1 END', [$validated['job_id']])
                ->orderBy('job_id')
                ->get();
        }

        return response()->json($budgets);
    }

    /**
     * Process tender data from request including file uploads
     */
    protected function processTenders(Request $request): array
    {
        $tenders = [];
        $tenderData = $request->input('tenders', []);

        foreach ($tenderData as $index => $tender) {
            if (empty($tender['contractor_id'])) {
                continue;
            }

            $tenderEntry = [
                'contractor_id' => $tender['contractor_id'],
                'tender_value' => $tender['tender_value'] ?? 0,
                'tender_duration' => $tender['tender_duration'] ?? null,
                'description' => $tender['description'] ?? null,
                'is_selected' => isset($tender['is_selected']) ? (bool) $tender['is_selected'] : false,
            ];

            // Handle file upload
            if ($request->hasFile("tenders.{$index}.attachment")) {
                $file = $request->file("tenders.{$index}.attachment");
                $tenderEntry['attachment_path'] = $file->store('uspk/tenders', 'public');
            }

            $tenders[] = $tenderEntry;
        }

        return $tenders;
    }
}
