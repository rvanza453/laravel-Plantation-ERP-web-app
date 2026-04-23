<?php

namespace Modules\SystemISPO\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrExternalDataRequestAttachment extends Model
{
    use HasFactory;

    public const KATEGORI_LAMPIRAN_OPTIONS = [
        'incoming_document',
        'outgoing_document',
    ];

    protected $table = 'hr_external_data_request_attachments';

    protected $fillable = [
        'external_data_request_id',
        'kategori_lampiran',
        'file_name',
        'file_path',
        'mime_type',
        'file_size',
        'uploaded_by',
        'uploaded_at',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    public function requestTicket()
    {
        return $this->belongsTo(HrExternalDataRequest::class, 'external_data_request_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
