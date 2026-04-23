<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('hr_external_data_requests', 'judul_permintaan')) {
            Schema::table('hr_external_data_requests', function (Blueprint $table): void {
                $table->string('judul_permintaan')->nullable()->after('nomor_referensi');
            });
        }

        DB::statement('ALTER TABLE hr_external_data_requests MODIFY tanggal_surat_masuk DATE NULL');
        DB::statement('ALTER TABLE hr_external_data_requests MODIFY pihak_peminta VARCHAR(255) NULL');
        DB::statement('ALTER TABLE hr_external_data_requests MODIFY kategori_data VARCHAR(255) NULL');
        DB::statement('ALTER TABLE hr_external_data_requests MODIFY deskripsi_permintaan TEXT NULL');
        DB::statement('ALTER TABLE hr_external_data_requests MODIFY deadline DATE NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE hr_external_data_requests MODIFY judul_permintaan VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE hr_external_data_requests MODIFY tanggal_surat_masuk DATE NOT NULL');
        DB::statement('ALTER TABLE hr_external_data_requests MODIFY pihak_peminta VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE hr_external_data_requests MODIFY kategori_data VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE hr_external_data_requests MODIFY deskripsi_permintaan TEXT NOT NULL');
        DB::statement('ALTER TABLE hr_external_data_requests MODIFY deadline DATE NOT NULL');
    }
};