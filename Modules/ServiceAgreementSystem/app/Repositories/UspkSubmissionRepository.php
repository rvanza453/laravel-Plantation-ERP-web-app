<?php

namespace Modules\ServiceAgreementSystem\Repositories;

use Illuminate\Support\Facades\Schema;
use Modules\ServiceAgreementSystem\Models\UspkSubmission;

class UspkSubmissionRepository
{
    public function getAll(?string $status = null, ?int $userId = null)
    {
        $withRelations = [
            'department',
            'subDepartment',
            'block',
            'job',
            'submitter',
            'legalUploader',
            'submitterSignedUploader',
            'qcAssigner',
            'tenders.contractor',
            'selectedTender.contractor',
        ];

        if (Schema::hasTable('uspk_qc_verifications')) {
            $withRelations[] = 'qcVerifications.verifier';
        }

        if (Schema::hasTable('uspk_qc_verification_logs')) {
            $withRelations[] = 'qcVerificationLogs.actor';
            $withRelations[] = 'qcVerificationLogs.verification.verifier';
        }

        if (Schema::hasTable('uspk_block_progresses')) {
            $withRelations[] = 'blockProgresses.block';
            $withRelations[] = 'blockProgresses.completedBy';
        }

        $query = UspkSubmission::with($withRelations)
            ->latest();

        if ($status) {
            $query->where('status', $status);
        }

        if ($userId) {
            $query->where('submitted_by', $userId);
        }

        return $query->paginate(15);
    }

    public function findById(int $id): UspkSubmission
    {
        $withRelations = [
            'department',
            'subDepartment',
            'block',
            'job',
            'budgetActivity',
            'submitter',
            'legalUploader',
            'submitterSignedUploader',
            'qcAssigner',
            'selectedTender.contractor',
            'tenders.contractor',
            'approvals.voteTender.contractor',
            'approvals.schema.steps.user',
            'approvals.approver',
        ];

        if (Schema::hasTable('uspk_qc_verifications')) {
            $withRelations[] = 'qcVerifications.verifier';
        }

        if (Schema::hasTable('uspk_qc_verification_logs')) {
            $withRelations[] = 'qcVerificationLogs.actor';
            $withRelations[] = 'qcVerificationLogs.verification.verifier';
        }

        if (Schema::hasTable('uspk_block_progresses')) {
            $withRelations[] = 'blockProgresses.block';
            $withRelations[] = 'blockProgresses.completedBy';
        }

        return UspkSubmission::with($withRelations)->findOrFail($id);
    }

    public function create(array $data): UspkSubmission
    {
        return UspkSubmission::create($data);
    }

    public function update(UspkSubmission $submission, array $data): UspkSubmission
    {
        $submission->update($data);
        return $submission->fresh();
    }

    public function delete(UspkSubmission $submission): void
    {
        $submission->delete();
    }
}
