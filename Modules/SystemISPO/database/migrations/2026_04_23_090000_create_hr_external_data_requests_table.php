<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_external_data_requests', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_referensi')->unique();
            $table->string('judul_permintaan')->nullable();
            $table->date('tanggal_surat_masuk')->nullable();
            $table->string('pihak_peminta')->nullable();
            $table->string('kategori_data')->nullable();
            $table->text('deskripsi_permintaan')->nullable();
            $table->date('deadline')->nullable();
            $table->foreignId('pic_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status_proses')->default('menunggu');
            $table->text('catatan_internal')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['status_proses', 'deadline']);
            $table->index('tanggal_surat_masuk');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_external_data_requests');
    }
};
