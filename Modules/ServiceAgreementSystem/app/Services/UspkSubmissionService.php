<?php

namespace Modules\ServiceAgreementSystem\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Modules\ServiceAgreementSystem\Models\UspkBlockProgress;
use Modules\ServiceAgreementSystem\Models\UspkSubmission;
use Modules\ServiceAgreementSystem\Models\UspkTender;
use Modules\ServiceAgreementSystem\Models\Site;
use Modules\ServiceAgreementSystem\Repositories\UspkSubmissionRepository;

class UspkSubmissionService
{
    public function __construct(
        protected UspkSubmissionRepository $repository
    ) {}

    public function getAll(?string $status = null, ?int $userId = null)
    {
        return $this->repository->getAll($status, $userId);
    }

    public function findById(int $id): UspkSubmission
    {
        return $this->repository->findById($id);
    }

    public function reconcileWinnerFromFinalApproval(UspkSubmission $submission): void
    {
        if (!Schema::hasColumn('uspk_approvals', 'vote_tender_id')) {
            Log::warning('Skip final approval reconciliation: vote_tender_id column is missing.', [
                'uspk_submission_id' => $submission->id,
            ]);

            return;
        }

        $maxApprovedLevel = (int) $submission->approvals()
            ->where('status', 'approved')
            ->max('level');

        if ($maxApprovedLevel <= 0) {
            return;
        }

        $winnerTenderId = $submission->approvals()
            ->where('status', 'approved')
            ->where('level', $maxApprovedLevel)
            ->whereNotNull('vote_tender_id')
            ->orderByDesc('id')
            ->value('vote_tender_id');

        if (!$winnerTenderId) {
            return;
        }

        $currentSelectedId = $submission->tenders()->where('is_selected', true)->value('id');

        if ((int) $currentSelectedId === (int) $winnerTenderId) {
            return;
        }

        DB::transaction(function () use ($submission, $winnerTenderId) {
            $submission->tenders()->update(['is_selected' => false]);
            $submission->tenders()->whereKey($winnerTenderId)->update(['is_selected' => true]);
        });
    }

    /**
     * Buat USPK baru sebagai draft
     */
    public function store(array $data, array $tenders): UspkSubmission
    {
        return DB::transaction(function () use ($data, $tenders) {
            $data = $this->normalizeBlockData($data);
            $data['uspk_number'] = $this->generateUspkNumber($data['department_id'] ?? null);
            $data['status'] = UspkSubmission::STATUS_DRAFT;
            $data['submitted_by'] = auth()->id();

            $submission = $this->repository->create($data);
            $this->syncBlockProgressRows($submission, $data['block_ids'] ?? []);

            // Simpan tender pembanding
            foreach ($tenders as $tender) {
                if (!empty($tender['contractor_id'])) {
                    $submission->tenders()->create($tender);
                }
            }

            Log::info('USPK Created', ['uspk_id' => $submission->id, 'number' => $submission->uspk_number]);

            return $submission;
        });
    }

    /**
     * Update USPK draft
     */
    public function update(UspkSubmission $submission, array $data, array $tenders): UspkSubmission
    {
        return DB::transaction(function () use ($submission, $data, $tenders) {
            $data = $this->normalizeBlockData($data);
            $this->repository->update($submission, $data);
            $this->syncBlockProgressRows($submission, $data['block_ids'] ?? []);

            // Hapus semua tender lama dan buat ulang
            $submission->tenders()->delete();

            foreach ($tenders as $tender) {
                if (!empty($tender['contractor_id'])) {
                    $submission->tenders()->create($tender);
                }
            }

            Log::info('USPK Updated', ['uspk_id' => $submission->id]);

            return $submission->fresh(['tenders']);
        });
    }

    /**
     * Submit USPK (ubah status dari draft ke submitted)
     */
    public function submit(UspkSubmission $submission): UspkSubmission
    {
        if (!$submission->isSubmittable()) {
            throw new \Exception('USPK tidak dapat disubmit. Pastikan minimal ada 1 tender pembanding.');
        }

        return DB::transaction(function () use ($submission) {
            $schema = $submission->department?->approvalSchemas()
                ->where('is_active', true)
                ->with('steps')
                ->first();

            if (!$schema) {
                throw new \Exception('Gagal submit: Tidak ada skema approval aktif yang ditetapkan untuk departemen ini.');
            }

            if ($schema->steps->isEmpty()) {
                throw new \Exception('Gagal submit: Skema approval tidak memiliki tahapan approval.');
            }

            $submission->update([
                'status' => UspkSubmission::STATUS_SUBMITTED,
                'submitted_at' => now(),
            ]);

            // Generate UspkApproval rows berdasarkan steps
            foreach ($schema->steps as $step) {
                $submission->approvals()->create([
                    'schema_id' => $schema->id,
                    'level' => $step->level,
                    'status' => \Modules\ServiceAgreementSystem\Models\UspkApproval::STATUS_PENDING,
                    'user_id' => $step->user_id, // Assigned approver
                ]);
            }

            Log::info('USPK Submitted with Pending Schema Approvals generated', [
                'uspk_id' => $submission->id, 
                'schema_id' => $schema->id,
                'step_count' => $schema->steps->count()
            ]);

            return $submission->fresh(['approvals']);
        });
    }

    public function delete(UspkSubmission $submission): void
    {
        if (!$submission->isEditable()) {
            throw new \Exception('USPK yang sudah disubmit tidak dapat dihapus.');
        }

        $this->repository->delete($submission);
        Log::info('USPK Deleted', ['uspk_id' => $submission->id]);
    }

    /**
     * Generate nomor USPK: 001/SITE-USPK/III/2026
     */
    protected function generateUspkNumber(?int $departmentId = null): string
    {
        $year = now()->year;
        $month = now()->month;
        $romanMonths = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        $romanMonth = $romanMonths[$month - 1];

        // Ambil kode site dari department
        $siteCode = 'SITE';
        if ($departmentId) {
            $department = \Modules\ServiceAgreementSystem\Models\Department::with('site')->find($departmentId);
            if ($department && $department->site) {
                $siteCode = $department->site->code;
            }
        }

        // Hitung sequence per tahun
        $lastSubmission = UspkSubmission::whereYear('created_at', $year)
            ->orderByDesc('id')
            ->first();

        if ($lastSubmission) {
            $lastNumber = (int) substr($lastSubmission->uspk_number, 0, 3);
            $sequence = $lastNumber + 1;
        } else {
            $sequence = 1;
        }

        $sequenceFormatted = str_pad($sequence, 3, '0', STR_PAD_LEFT);

        return "{$sequenceFormatted}/{$siteCode}-USPK/{$romanMonth}/{$year}";
    }

    protected function normalizeBlockData(array $data): array
    {
        $blockIds = $data['block_ids'] ?? [];

        if (!is_array($blockIds)) {
            $blockIds = array_filter([(string) $blockIds]);
        }

        $blockIds = array_values(array_filter($blockIds, fn ($id) => !empty($id)));

        if (!empty($blockIds)) {
            $data['block_id'] = (int) $blockIds[0];
        }

        $data['block_ids'] = array_map('intval', $blockIds);

        return $data;
    }

    protected function syncBlockProgressRows(UspkSubmission $submission, array $blockIds): void
    {
        $normalizedBlockIds = collect($blockIds)
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        if ($normalizedBlockIds->isEmpty()) {
            $submission->blockProgresses()->delete();
            return;
        }

        $submission->blockProgresses()
            ->whereNotIn('block_id', $normalizedBlockIds->all())
            ->delete();

        $existingBlockIds = $submission->blockProgresses()
            ->whereIn('block_id', $normalizedBlockIds->all())
            ->pluck('block_id')
            ->map(fn ($id) => (int) $id);

        $missingBlockIds = $normalizedBlockIds->diff($existingBlockIds);

        foreach ($missingBlockIds as $blockId) {
            $submission->blockProgresses()->create([
                'block_id' => $blockId,
                'status' => UspkBlockProgress::STATUS_PENDING,
            ]);
        }
    }
}
