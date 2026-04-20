<?php

namespace Modules\ServiceAgreementSystem\Repositories;

use Modules\ServiceAgreementSystem\Models\UspkSubmission;

class UspkSubmissionRepository
{
    public function getAll(?string $status = null, ?int $userId = null)
    {
        $query = UspkSubmission::with([
            'department',
            'subDepartment',
            'block',
            'job',
            'submitter',
            'legalUploader',
            'submitterSignedUploader',
            'qcAssigner',
            'qcVerifications.verifier',
            'qcVerificationLogs.actor',
            'qcVerificationLogs.verification.verifier',
            'blockProgresses.block',
            'blockProgresses.completedBy',
            'tenders.contractor',
            'selectedTender.contractor',
        ])
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
        return UspkSubmission::with([
            'department',
            'subDepartment',
            'block',
            'job',
            'budgetActivity',
            'submitter',
            'legalUploader',
            'submitterSignedUploader',
            'qcAssigner',
            'qcVerifications.verifier',
            'qcVerificationLogs.actor',
            'qcVerificationLogs.verification.verifier',
            'blockProgresses.block',
            'blockProgresses.completedBy',
            'selectedTender.contractor',
            'tenders.contractor',
            'approvals.voteTender.contractor',
            'approvals.schema.steps.user',
            'approvals.approver',
        ])->findOrFail($id);
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
