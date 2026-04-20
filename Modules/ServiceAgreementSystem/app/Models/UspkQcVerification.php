<?php

namespace Modules\ServiceAgreementSystem\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UspkQcVerification extends Model
{
    protected $fillable = [
        'uspk_submission_id',
        'user_id',
        'assigned_by',
        'assigned_at',
        'status',
        'comment',
        'verified_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    public function submission(): BelongsTo
    {
        return $this->belongsTo(UspkSubmission::class, 'uspk_submission_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(UspkQcVerificationLog::class, 'uspk_qc_verification_id');
    }
}
