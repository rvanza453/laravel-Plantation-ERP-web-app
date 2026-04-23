<?php

namespace Modules\ServiceAgreementSystem\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UspkBapp extends Model
{
    protected $fillable = [
        'bapp_number',
        'bapp_date',
        'job_id',
        'contractor_id',
        'document_link',
        'uploaded_by',
    ];

    protected $casts = [
        'bapp_date' => 'date',
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function contractor(): BelongsTo
    {
        return $this->belongsTo(Contractor::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(UspkSubmission::class, 'uspk_bapp_id');
    }
}
