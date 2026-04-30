<?php

namespace Modules\PrSystem\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'requester_id',
        'decision_by',
        'code',
        'name',
        'unit',
        'category',
        'price_estimation',
        'min_stock',
        'reference_link',
        'status',
        'site_id',
    ];

    protected $casts = [
        'price_estimation' => 'decimal:2',
        'min_stock' => 'integer',
    ];

    // Mutators for uppercase
    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = strtoupper($value);
    }
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = strtoupper($value);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function decisionBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decision_by');
    }
}