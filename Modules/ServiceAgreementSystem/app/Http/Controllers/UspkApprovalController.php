<?php

namespace Modules\ServiceAgreementSystem\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\ServiceAgreementSystem\Models\UspkSubmission;
use Modules\ServiceAgreementSystem\Services\UspkApprovalService;

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

        $pendingUspks = UspkSubmission::query()
            ->whereHas('approvals', function ($query) use ($userId, $isSasAdmin) {
                $query->whereIn('status', ['pending', 'on_hold']);

                if (!$isSasAdmin) {
                    $query->where('user_id', $userId);
                }
            })
            ->with(['department', 'subDepartment', 'block', 'submitter'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

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
}
