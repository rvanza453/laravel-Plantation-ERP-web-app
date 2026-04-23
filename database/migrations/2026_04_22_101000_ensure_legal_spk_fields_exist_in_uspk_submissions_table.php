<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('uspk_submissions')) {
            return;
        }

        Schema::table('uspk_submissions', function (Blueprint $table) {
            if (!Schema::hasColumn('uspk_submissions', 'legal_spk_document_path')) {
                $table->string('legal_spk_document_path')->nullable();
            }

            if (!Schema::hasColumn('uspk_submissions', 'legal_spk_uploaded_by')) {
                $table->foreignId('legal_spk_uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('uspk_submissions', 'legal_spk_uploaded_at')) {
                $table->timestamp('legal_spk_uploaded_at')->nullable();
            }

            if (!Schema::hasColumn('uspk_submissions', 'legal_spk_notes')) {
                $table->text('legal_spk_notes')->nullable();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('uspk_submissions')) {
            return;
        }

        Schema::table('uspk_submissions', function (Blueprint $table) {
            if (Schema::hasColumn('uspk_submissions', 'legal_spk_uploaded_by')) {
                $table->dropConstrainedForeignId('legal_spk_uploaded_by');
            }

            if (Schema::hasColumn('uspk_submissions', 'legal_spk_document_path')) {
                $table->dropColumn('legal_spk_document_path');
            }

            if (Schema::hasColumn('uspk_submissions', 'legal_spk_uploaded_at')) {
                $table->dropColumn('legal_spk_uploaded_at');
            }

            if (Schema::hasColumn('uspk_submissions', 'legal_spk_notes')) {
                $table->dropColumn('legal_spk_notes');
            }
        });
    }
};