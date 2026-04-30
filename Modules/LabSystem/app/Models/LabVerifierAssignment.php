<?php

namespace Modules\LabSystem\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class LabVerifierAssignment extends Model
{
    protected $fillable = [
        'user_id',
        'site_id',
        'assignment_type',
        'assignment_value',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(\Modules\SystemISPO\Models\Site::class);
    }

    /**
     * Scope to get active assignments only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if user is assigned for a specific parameter
     */
    public static function isAssignedTo(User $user, $parameterName = null, $category = null, $siteId = null)
    {
        $query = static::where('user_id', $user->id)->active();

        if ($siteId) {
            $query->where(fn($q) => $q->whereNull('site_id')->orWhere('site_id', $siteId));
        }

        // Check global assignment first
        if ($query->where('assignment_type', 'global')->exists()) {
            return true;
        }

        // Check parameter-specific assignment
        if ($parameterName) {
            if ($query->where('assignment_type', 'parameter')->where('assignment_value', $parameterName)->exists()) {
                return true;
            }
        }

        // Check category-specific assignment
        if ($category) {
            if ($query->where('assignment_type', 'category')->where('assignment_value', $category)->exists()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get all verifiers assigned to a specific batch parameter
     */
    public static function getVerifiersFor($parameterName = null, $category = null, $siteId = null)
    {
        return static::query()
            ->active()
            ->where(function ($q) use ($siteId) {
                if ($siteId) {
                    $q->whereNull('site_id')->orWhere('site_id', $siteId);
                }
            })
            ->where(function ($q) use ($parameterName, $category) {
                // Global verifiers
                $q->where('assignment_type', 'global');

                // Parameter-specific verifiers
                if ($parameterName) {
                    $q->orWhere(function ($subQ) use ($parameterName) {
                        $subQ->where('assignment_type', 'parameter')
                            ->where('assignment_value', $parameterName);
                    });
                }

                // Category-specific verifiers
                if ($category) {
                    $q->orWhere(function ($subQ) use ($category) {
                        $subQ->where('assignment_type', 'category')
                            ->where('assignment_value', $category);
                    });
                }
            })
            ->with('user')
            ->get();
    }
}
