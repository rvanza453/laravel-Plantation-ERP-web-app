<?php

namespace Modules\ServiceAgreementSystem\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UspkBlockProgress extends Model
{
    protected $table = 'uspk_block_progresses';

    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'uspk_submission_id',
        'block_id',
        'status',
        'deadline_at',
        'completed_at',
        'completed_by',
    ];

    protected $casts = [
        'deadline_at' => 'date',
        'completed_at' => 'datetime',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(UspkSubmission::class, 'uspk_submission_id');
    }

    public function block(): BelongsTo
    {
        return $this->belongsTo(Block::class, 'block_id');
    }

    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }
}
