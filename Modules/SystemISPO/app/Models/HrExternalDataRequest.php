<?php

namespace Modules\SystemISPO\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrExternalDataRequest extends Model
{
    use HasFactory;

    public const STATUS_OPTIONS = [
        'menunggu',
        'sedang_diproses',
        'menunggu_persetujuan_manajer',
        'selesai',
        'ditolak',
    ];

    public const PIHAK_PEMINTA_OPTIONS = [
        'auditor_eksternal',
        'regulator',
        'klien',
        'vendor',
        'internal_lainnya',
    ];

    public const KATEGORI_DATA_OPTIONS = [
        'kepatuhan_ispo',
        'dokumen_legal',
        'operasional_kebun',
        'keuangan',
        'lainnya',
    ];

    protected $table = 'hr_external_data_requests';

    protected $fillable = [
        'nomor_referensi',
        'judul_permintaan',
        'tanggal_surat_masuk',
        'pihak_peminta',
        'kategori_data',
        'deskripsi_permintaan',
        'deadline',
        'pic_user_id',
        'status_proses',
        'catatan_internal',
        'tanggal_selesai',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal_surat_masuk' => 'date',
        'deadline' => 'date',
        'tanggal_selesai' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model): void {
            if (empty($model->nomor_referensi)) {
                $model->nomor_referensi = static::generateNomorReferensi($model->tanggal_surat_masuk ?: now()->toDateString());
            }
        });
    }

    public static function generateNomorReferensi(string $tanggalSuratMasuk): string
    {
        $date = \Carbon\Carbon::parse($tanggalSuratMasuk);
        $prefix = sprintf('REQ-EXT-%s-%s-', $date->format('Y'), $date->format('m'));

        $lastNumber = static::query()
            ->where('nomor_referensi', 'like', $prefix . '%')
            ->selectRaw("MAX(CAST(RIGHT(nomor_referensi, 3) AS UNSIGNED)) as max_seq")
            ->value('max_seq');

        $next = ((int) $lastNumber) + 1;

        return $prefix . str_pad((string) $next, 3, '0', STR_PAD_LEFT);
    }

    public function picUser()
    {
        return $this->belongsTo(User::class, 'pic_user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function attachments()
    {
        return $this->hasMany(HrExternalDataRequestAttachment::class, 'external_data_request_id');
    }

    public function shareTokens()
    {
        return $this->hasMany(HrExternalDataRequestShareToken::class, 'external_data_request_id');
    }
}
