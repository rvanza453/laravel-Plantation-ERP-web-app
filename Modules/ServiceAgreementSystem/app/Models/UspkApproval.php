<?php

namespace Modules\ServiceAgreementSystem\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UspkApproval extends Model
{
    protected $fillable = [
        'uspk_submission_id',
        'schema_id',
        'vote_tender_id',
        'vote_tender_value',
        'vote_tender_duration',
        'vote_tender_description',
        'user_id',
        'level',
        'status',
        'comment',
        'approved_at',
        'previous_status',
        'rollback_by_user_id',
        'rollback_at',
    ];

    protected $casts = [
        'vote_tender_value' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_ON_HOLD = 'on_hold';

    public function submission(): BelongsTo
    {
        return $this->belongsTo(UspkSubmission::class, 'uspk_submission_id');
    }

    public function voteTender(): BelongsTo
    {
        return $this->belongsTo(UspkTender::class, 'vote_tender_id');
    }

    public function schema(): BelongsTo
    {
        return $this->belongsTo(UspkApprovalSchema::class, 'schema_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
