<?php

namespace Modules\ServiceAgreementSystem\Models;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\ServiceAgreementSystem\Models\UspkBlockProgress;
use Modules\ServiceAgreementSystem\Models\UspkQcVerification;
use Modules\ServiceAgreementSystem\Models\UspkQcVerificationLog;

class UspkSubmission extends Model
{
    protected $fillable = [
        'uspk_number',
        'title',
        'description',
        'location',
        'work_type',
        'department_id',
        'sub_department_id',
        'block_id',
        'block_ids',
        'job_id',
        'uspk_budget_activity_id',
        'estimated_value',
        'estimated_duration',
        'status',
        'submitted_by',
        'submitted_at',
        'legal_spk_document_path',
        'legal_spk_uploaded_by',
        'legal_spk_uploaded_at',
        'legal_spk_notes',
        'submitter_signed_spk_document_path',
        'submitter_signed_spk_uploaded_by',
        'submitter_signed_spk_uploaded_at',
        'qc_status',
        'qc_assigned_by',
        'qc_assigned_at',
        'work_reported_completed_at',
    ];

    protected $casts = [
        'estimated_value' => 'decimal:2',
        'submitted_at' => 'datetime',
        'legal_spk_uploaded_at' => 'datetime',
        'submitter_signed_spk_uploaded_at' => 'datetime',
        'qc_assigned_at' => 'datetime',
        'work_reported_completed_at' => 'datetime',
        'block_ids' => 'array',
    ];

    // Status constants
    const STATUS_DRAFT = 'draft';
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_IN_REVIEW = 'in_review';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    // QC flow status constants
    const QC_STATUS_PENDING_ASSIGNMENT = 'pending_assignment';
    const QC_STATUS_ASSIGNED = 'assigned';
    const QC_STATUS_IN_VERIFICATION = 'in_verification';
    const QC_STATUS_VERIFIED = 'verified';
    const QC_STATUS_REVISION_REQUIRED = 'revision_required';

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function subDepartment(): BelongsTo
    {
        return $this->belongsTo(SubDepartment::class);
    }

    public function block(): BelongsTo
    {
        return $this->belongsTo(Block::class);
    }

    /**
     * Accessor untuk daftar blok (multi-block via block_ids) dengan fallback ke block lama.
     */
    public function getBlocksAttribute(): Collection
    {
        $blockIds = collect($this->block_ids ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->values();

        if ($blockIds->isNotEmpty()) {
            $orderMap = $blockIds->flip();

            return Block::query()
                ->whereIn('id', $blockIds->all())
                ->get(['id', 'name', 'code'])
                ->sortBy(fn ($block) => $orderMap[(int) $block->id] ?? PHP_INT_MAX)
                ->values();
        }

        if ($this->relationLoaded('block') ? $this->block : $this->block()->exists()) {
            return collect([$this->block]);
        }

        return collect();
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function budgetActivity(): BelongsTo
    {
        return $this->belongsTo(UspkBudgetActivity::class, 'uspk_budget_activity_id');
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function legalUploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'legal_spk_uploaded_by');
    }

    public function submitterSignedUploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitter_signed_spk_uploaded_by');
    }

    public function qcAssigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'qc_assigned_by');
    }

    public function tenders(): HasMany
    {
        return $this->hasMany(UspkTender::class);
    }

    public function selectedTender(): HasOne
    {
        return $this->hasOne(UspkTender::class)->where('is_selected', true);
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(UspkApproval::class)->orderBy('level');
    }

    public function qcVerifications(): HasMany
    {
        return $this->hasMany(UspkQcVerification::class, 'uspk_submission_id');
    }

    public function qcVerificationLogs(): HasMany
    {
        return $this->hasMany(UspkQcVerificationLog::class, 'uspk_submission_id')->latest();
    }

    public function blockProgresses(): HasMany
    {
        return $this->hasMany(UspkBlockProgress::class, 'uspk_submission_id');
    }

    /**
     * Cek apakah USPK masih bisa diedit
     */
    public function isEditable(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    /**
     * Cek apakah USPK bisa disubmit
     */
    public function isSubmittable(): bool
    {
        return $this->status === self::STATUS_DRAFT && $this->tenders()->count() >= 1;
    }

    public function hasFinalSpkDocument(): bool
    {
        return !empty($this->legal_spk_document_path);
    }

    public function hasSubmitterSignedSpkDocument(): bool
    {
        return !empty($this->submitter_signed_spk_document_path);
    }

    public function getBlockProgressSummaryAttribute(): array
    {
        $total = $this->blocks->count();
        $completed = $this->blockProgresses
            ->where('status', UspkBlockProgress::STATUS_COMPLETED)
            ->count();

        return [
            'total_blocks' => $total,
            'completed_blocks' => $completed,
            'percent' => $total > 0 ? (int) round(($completed / $total) * 100) : 0,
        ];
    }
}
