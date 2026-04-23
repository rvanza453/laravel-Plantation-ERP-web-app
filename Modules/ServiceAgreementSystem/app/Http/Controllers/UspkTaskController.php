<?php

namespace Modules\ServiceAgreementSystem\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\ServiceAgreementSystem\Models\UspkSubmission;

class UspkTaskController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $userId = $user->id;
        $role = strtolower(trim((string) $user?->moduleRole('sas')));
        
        $isAdmin = $role === 'admin' || $user?->hasAnyRole(['Admin', 'Super Admin']);
        // Approver logic
        $isApprover = $role === 'approver' || $isAdmin;
        // Legal logic
        $isLegal = in_array($role, ['legal', 'admin'], true) || $user?->hasAnyRole(['Legal', 'Admin', 'Super Admin']);
        // QC logic
        $isQcCoordinator = in_array($role, ['qc', 'admin'], true) || $user?->hasAnyRole(['Admin', 'Super Admin']);
        $isStaff = $role === 'staff' || str_contains($role, 'pengaju');
        $isQcOrStaff = $isQcCoordinator || $isStaff || $isAdmin; // Staff handles reporting completion

        // 1. Pending Approvals
        $pendingApprovals = collect();
        if ($isApprover) {
            $query = UspkSubmission::query()
                ->whereHas('approvals', function ($q) use ($userId, $isAdmin) {
                    $q->whereIn('status', ['pending', 'on_hold']);
                    if (!$isAdmin) {
                        $q->where('user_id', $userId);
                    }
                })
                ->with(['department', 'subDepartment', 'block', 'submitter', 'approvals'])
                ->orderByDesc('created_at');
                
            $allUspks = $query->get();
            $pendingApprovals = $allUspks->filter(function ($uspk) use ($userId, $isAdmin) {
                $minPendingLevel = $uspk->approvals->whereIn('status', ['pending', 'on_hold'])->min('level');
                if ($minPendingLevel === null) return false;
                
                return $uspk->approvals
                    ->where('level', $minPendingLevel)
                    ->filter(function ($approval) use ($userId, $isAdmin) {
                        return $isAdmin || (int) $approval->user_id === (int) $userId;
                    })->count() > 0;
            });
        }
        
        // 2. Legal Review Tasks
        $legalTasks = collect();
        if ($isLegal) {
            $legalTasks = UspkSubmission::query()
                ->where('status', UspkSubmission::STATUS_APPROVED)
                ->whereNull('legal_spk_document_path')
                ->with(['department', 'subDepartment', 'submitter', 'selectedTender.contractor'])
                ->latest()
                ->get();
        }
        
        // 3. QC Tasks (Assign Verifier, Report Completed, Verification)
        $qcTasks = collect();
        if ($isQcOrStaff) {
            $queryQC = UspkSubmission::query()
                ->whereNotNull('submitter_signed_spk_document_path')
                ->whereNotNull('qc_status')
                ->where('qc_status', '!=', UspkSubmission::QC_STATUS_VERIFIED) // Don't show fully verified tasks here
                ->with(['department', 'subDepartment', 'submitter', 'qcAssigner', 'qcVerifications.verifier'])
                ->orderByDesc('updated_at');
            
            if (!$isQcCoordinator) {
                if ($isStaff) {
                    $queryQC->where('submitted_by', $userId);
                } else {
                    $queryQC->whereHas('qcVerifications', function ($q) use ($userId) {
                        $q->where('user_id', $userId);
                    });
                }
            }
            
            $qcTasks = $queryQC->get();
        }
        
        return view('serviceagreementsystem::uspk-task.index', compact('pendingApprovals', 'legalTasks', 'qcTasks'));
    }
}
