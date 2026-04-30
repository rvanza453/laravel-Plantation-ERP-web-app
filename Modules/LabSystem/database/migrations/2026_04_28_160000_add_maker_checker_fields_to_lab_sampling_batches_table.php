<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('lab_sampling_batches')) {
            return;
        }

        Schema::table('lab_sampling_batches', function (Blueprint $table): void {
            if (!Schema::hasColumn('lab_sampling_batches', 'verified_by')) {
                $table->foreignId('verified_by')->nullable()->after('sampler_user_id')->constrained('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('lab_sampling_batches', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('verified_by');
            }

            if (!Schema::hasColumn('lab_sampling_batches', 'reject_reason')) {
                $table->text('reject_reason')->nullable()->after('verified_at');
            }

            if (!Schema::hasColumn('lab_sampling_batches', 'verifier_notes')) {
                $table->text('verifier_notes')->nullable()->after('reject_reason');
            }
        });

        // Keep backward compatibility for old values while enabling maker-checker lifecycle.
        DB::statement("ALTER TABLE lab_sampling_batches MODIFY status ENUM('draft','pending','approved','rejected','sampled','in_analysis','completed') NOT NULL DEFAULT 'draft'");
    }

    public function down(): void
    {
        if (!Schema::hasTable('lab_sampling_batches')) {
            return;
        }

        DB::statement("ALTER TABLE lab_sampling_batches MODIFY status ENUM('draft','sampled','in_analysis','completed') NOT NULL DEFAULT 'draft'");

        Schema::table('lab_sampling_batches', function (Blueprint $table): void {
            if (Schema::hasColumn('lab_sampling_batches', 'verifier_notes')) {
                $table->dropColumn('verifier_notes');
            }

            if (Schema::hasColumn('lab_sampling_batches', 'reject_reason')) {
                $table->dropColumn('reject_reason');
            }

            if (Schema::hasColumn('lab_sampling_batches', 'verified_at')) {
                $table->dropColumn('verified_at');
            }

            if (Schema::hasColumn('lab_sampling_batches', 'verified_by')) {
                $table->dropConstrainedForeignId('verified_by');
            }
        });
    }
};
