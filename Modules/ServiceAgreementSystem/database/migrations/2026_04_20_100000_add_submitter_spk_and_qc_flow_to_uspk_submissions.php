<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('uspk_submissions', function (Blueprint $table) {
            if (!Schema::hasColumn('uspk_submissions', 'submitter_signed_spk_document_path')) {
                $table->string('submitter_signed_spk_document_path')->nullable();
            }

            if (!Schema::hasColumn('uspk_submissions', 'submitter_signed_spk_uploaded_by')) {
                $table->foreignId('submitter_signed_spk_uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('uspk_submissions', 'submitter_signed_spk_uploaded_at')) {
                $table->timestamp('submitter_signed_spk_uploaded_at')->nullable();
            }

            if (!Schema::hasColumn('uspk_submissions', 'qc_status')) {
                $table->string('qc_status', 40)->nullable();
            }

            if (!Schema::hasColumn('uspk_submissions', 'qc_assigned_by')) {
                $table->foreignId('qc_assigned_by')->nullable()->constrained('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('uspk_submissions', 'qc_assigned_at')) {
                $table->timestamp('qc_assigned_at')->nullable();
            }

            if (!Schema::hasColumn('uspk_submissions', 'work_reported_completed_at')) {
                $table->timestamp('work_reported_completed_at')->nullable();
            }
        });

        if (!Schema::hasTable('uspk_qc_verifications')) {
            Schema::create('uspk_qc_verifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('uspk_submission_id')->constrained('uspk_submissions')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('assigned_at')->nullable();
                $table->string('status', 30)->default('pending');
                $table->text('comment')->nullable();
                $table->timestamp('verified_at')->nullable();
                $table->timestamps();

                $table->unique(['uspk_submission_id', 'user_id'], 'uspk_qc_verifications_submission_user_unique');
                $table->index(['uspk_submission_id', 'status'], 'uspk_qc_verifications_submission_status_idx');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('uspk_qc_verifications');

        Schema::table('uspk_submissions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('submitter_signed_spk_uploaded_by');
            $table->dropConstrainedForeignId('qc_assigned_by');
            $table->dropColumn([
                'submitter_signed_spk_document_path',
                'submitter_signed_spk_uploaded_at',
                'qc_status',
                'qc_assigned_at',
                'work_reported_completed_at',
            ]);
        });
    }
};
