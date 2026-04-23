<?php

namespace Modules\ServiceAgreementSystem\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\ServiceAgreementSystem\Models\UspkSubmission;
use Modules\ServiceAgreementSystem\Services\UspkApprovalService;
use Modules\PrSystem\Helpers\ActivityLogger;

class UspkApprovalController extends Controller
{
    public function __construct(
        protected UspkApprovalService $approvalService
    ) {}

    public function index()
    {
        $userId = auth()->id();

        $user = auth()->user();
        $role = strtolower(trim((string) $user?->moduleRole('sas')));
        $isSasAdmin = $role === 'admin' || $user?->hasAnyRole(['Admin', 'Super Admin']);

        // Get USPKs where there are any pending/on_hold approvals
        // Using indexes: (uspk_submission_id, level, status) and (user_id, status, level)
        $query = UspkSubmission::query()
            ->whereHas('approvals', function ($query) use ($userId, $isSasAdmin) {
                $query->whereIn('status', ['pending', 'on_hold']);

                if (!$isSasAdmin) {
                    $query->where('user_id', $userId);
                }
            })
            ->with([
                'department:id,name',
                'subDepartment:id,name',
                'block:id,name',
                'submitter:id,name',
                'approvals:id,uspk_submission_id,level,status,user_id,approved_at',
            ])
            ->select(['uspk_submissions.*']) // Explicit select to help optimizer
            ->orderBy('created_at', 'desc');

        // Load all to filter by minimum level
        $allUspks = $query->get();

        // Filter to only show at minimum pending level
        $filteredUspks = $allUspks->filter(function ($uspk) use ($userId, $isSasAdmin) {
            $minPendingLevel = $uspk->approvals
                ->whereIn('status', ['pending', 'on_hold'])
                ->min('level');

            if ($minPendingLevel === null) {
                return false; // No pending approvals (shouldn't happen, but safety check)
            }

            // Check if user has approval at this minimum level
            $hasAtMinLevel = $uspk->approvals
                ->where('level', $minPendingLevel)
                ->filter(function ($approval) use ($userId, $isSasAdmin) {
                    return $isSasAdmin || (int) $approval->user_id === (int) $userId;
                })
                ->count() > 0;

            return $hasAtMinLevel;
        });

        // Paginate the filtered results
        $page = request()->get('page', 1);
        $perPage = 10;
        $total = $filteredUspks->count();
        $items = $filteredUspks->forPage($page, $perPage)->values();

        $pendingUspks = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            [
                'path' => route('sas.uspk-approvals.index'),
                'query' => request()->query(),
            ]
        );

        return view('serviceagreementsystem::uspk-approval.index', compact('pendingUspks'));
    }

    public function approve(Request $request, UspkSubmission $uspk)
    {
        $request->validate([
            'comment' => 'nullable|string',
            'selected_tender_id' => 'required|exists:uspk_tenders,id',
            'vote_tender_value' => 'nullable|numeric|min:0',
            'vote_tender_duration' => 'nullable|integer|min:1',
            'vote_tender_description' => 'nullable|string',
        ]);

        $this->approvalService->approve($uspk, auth()->id(), $request->comment, $request->selected_tender_id, [
            'vote_tender_id' => $request->selected_tender_id,
            'vote_tender_value' => $request->vote_tender_value,
            'vote_tender_duration' => $request->vote_tender_duration,
            'vote_tender_description' => $request->vote_tender_description,
        ]);

        return redirect()->route('sas.uspk.show', $uspk)->with('success', 'Voting dan approval USPK berhasil disimpan.');
    }

    public function hold(Request $request, UspkSubmission $uspk)
    {
        $request->validate([
            'comment' => 'nullable|string',
            'selected_tender_id' => 'required|exists:uspk_tenders,id',
            'vote_tender_value' => 'nullable|numeric|min:0',
            'vote_tender_duration' => 'nullable|integer|min:1',
            'vote_tender_description' => 'nullable|string',
        ]);

        $this->approvalService->hold($uspk, auth()->id(), $request->comment, $request->selected_tender_id, [
            'vote_tender_id' => $request->selected_tender_id,
            'vote_tender_value' => $request->vote_tender_value,
            'vote_tender_duration' => $request->vote_tender_duration,
            'vote_tender_description' => $request->vote_tender_description,
        ]);

        return redirect()->route('sas.uspk.show', $uspk)->with('success', 'Status hold dan voting USPK berhasil disimpan.');
    }

    public function reject(Request $request, UspkSubmission $uspk)
    {
        $request->validate([
            'comment' => 'required|string',
            'selected_tender_id' => 'nullable|exists:uspk_tenders,id',
            'vote_tender_value' => 'nullable|numeric|min:0',
            'vote_tender_duration' => 'nullable|integer|min:1',
            'vote_tender_description' => 'nullable|string',
        ]);

        $this->approvalService->reject($uspk, auth()->id(), $request->comment, [
            'vote_tender_id' => $request->selected_tender_id,
            'vote_tender_value' => $request->vote_tender_value,
            'vote_tender_duration' => $request->vote_tender_duration,
            'vote_tender_description' => $request->vote_tender_description,
        ]);

        return redirect()->route('sas.uspk.show', $uspk)->with('success', 'USPK berhasil di-reject.');
    }

    public function rollback(Request $request, UspkSubmission $uspk)
    {
        $user = auth()->user();
        $role = strtolower(trim((string) $user?->moduleRole('sas')));
        $isSasAdmin = $role === 'admin' || $user?->hasAnyRole(['Admin', 'Super Admin']);

        $validated = $request->validate([
            'approval_id' => 'nullable|integer',
        ]);

        $approvalQuery = $uspk->approvals()
            ->whereIn('status', ['approved', 'rejected', 'on_hold'])
            ->orderByDesc('approved_at')
            ->orderByDesc('id');

        if (!empty($validated['approval_id'])) {
            $approvalQuery->where('id', (int) $validated['approval_id']);
        }

        $approval = $approvalQuery
            ->first();

        if (!$approval) {
            return back()->with('error', 'Approval tidak ditemukan atau tidak dapat di-rollback.');
        }

        $isOwner = (int) $approval->user_id === (int) auth()->id();

        if (!$isSasAdmin && !$isOwner) {
            abort(403, 'Anda tidak dapat merollback approval milik pengguna lain.');
        }

        if (!$isSasAdmin) {
            $higherLevelActed = $uspk->approvals()
                ->where('level', '>', $approval->level)
                ->where('status', '!=', 'pending')
                ->exists();

            if ($higherLevelActed) {
                return back()->with('error', 'Keputusan tidak dapat dirubah karena approval pada jenjang berikutnya sudah memproses USPK ini.');
            }
        }

        $previousStatus = (string) $approval->status;
        $approval->update([
            'status' => 'pending',
            'previous_status' => $previousStatus,
            'rollback_by_user_id' => auth()->id(),
            'rollback_at' => now(),
            'approved_at' => null,
        ]);

        if ($uspk->status === UspkSubmission::STATUS_APPROVED) {
            $uspk->update(['status' => UspkSubmission::STATUS_IN_REVIEW]);
        }

        ActivityLogger::log(
            action: 'Rollback Approval SAS',
            description: sprintf(
                'Admin rollback approval USPK %s pada level %d (status sebelumnya: %s).',
                (string) ($uspk->uspk_number ?? ('#' . $uspk->id)),
                (int) $approval->level,
                $previousStatus
            ),
            subject: $uspk,
            context: [
                'system' => 'SAS',
                'route_name' => request()->route()?->getName(),
                'http_method' => request()->method(),
                'url' => request()->fullUrl(),
            ]
        );

        return redirect()->route('sas.uspk.show', $uspk)->with('success', 'Rollback approval berhasil. Approval kembali ke status pending.');
    }
}
