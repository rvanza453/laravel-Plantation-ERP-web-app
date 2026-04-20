<?php

namespace Modules\ServiceAgreementSystem\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UspkQcVerificationLog extends Model
{
    protected $fillable = [
        'uspk_submission_id',
        'uspk_qc_verification_id',
        'actor_id',
        'action',
        'status_before',
        'status_after',
        'comment',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(UspkSubmission::class, 'uspk_submission_id');
    }

    public function verification(): BelongsTo
    {
        return $this->belongsTo(UspkQcVerification::class, 'uspk_qc_verification_id');
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
