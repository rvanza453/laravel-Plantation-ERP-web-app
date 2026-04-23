<?php

namespace Modules\SystemISPO\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrExternalDataRequestAccessLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'hr_external_data_request_access_logs';

    protected $fillable = [
        'share_token_id',
        'external_data_request_id',
        'action',
        'status_code',
        'ip_address',
        'user_agent',
        'accessed_at',
    ];

    protected $casts = [
        'accessed_at' => 'datetime',
    ];

    public function shareToken()
    {
        return $this->belongsTo(HrExternalDataRequestShareToken::class, 'share_token_id');
    }

    public function requestTicket()
    {
        return $this->belongsTo(HrExternalDataRequest::class, 'external_data_request_id');
    }
}
