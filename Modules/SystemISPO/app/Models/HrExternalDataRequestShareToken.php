<?php

namespace Modules\SystemISPO\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrExternalDataRequestShareToken extends Model
{
    use HasFactory;

    protected $table = 'hr_external_data_request_share_tokens';

    protected $fillable = [
        'external_data_request_id',
        'token_hash',
        'token_hint',
        'allow_download',
        'allow_preview_only',
        'max_views',
        'view_count',
        'expires_at',
        'last_accessed_at',
        'revoked_at',
        'created_by',
    ];

    protected $casts = [
        'allow_download' => 'boolean',
        'allow_preview_only' => 'boolean',
        'expires_at' => 'datetime',
        'last_accessed_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    public static function generatePlainToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    public static function hashToken(string $plainToken): string
    {
        return hash('sha256', $plainToken);
    }

    public function requestTicket()
    {
        return $this->belongsTo(HrExternalDataRequest::class, 'external_data_request_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function accessLogs()
    {
        return $this->hasMany(HrExternalDataRequestAccessLog::class, 'share_token_id');
    }

    public function isRevoked(): bool
    {
        return !is_null($this->revoked_at);
    }

    public function isExpired(): bool
    {
        return !is_null($this->expires_at) && $this->expires_at->isPast();
    }

    public function maxViewsReached(): bool
    {
        return !is_null($this->max_views) && $this->view_count >= $this->max_views;
    }

    public function canBeUsed(): bool
    {
        return !$this->isRevoked() && !$this->isExpired() && !$this->maxViewsReached();
    }
}
