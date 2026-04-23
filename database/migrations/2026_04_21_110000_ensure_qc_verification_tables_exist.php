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

        if (!Schema::hasTable('uspk_qc_verification_logs')) {
            Schema::create('uspk_qc_verification_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('uspk_submission_id')->constrained('uspk_submissions')->cascadeOnDelete();
                $table->foreignId('uspk_qc_verification_id')->nullable()->constrained('uspk_qc_verifications')->nullOnDelete();
                $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('action', 80);
                $table->string('status_before', 40)->nullable();
                $table->string('status_after', 40)->nullable();
                $table->text('comment')->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();

                $table->index(['uspk_submission_id', 'created_at'], 'uspk_qc_logs_submission_created_idx');
                $table->index(['action', 'created_at'], 'uspk_qc_logs_action_created_idx');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('uspk_qc_verification_logs');
        Schema::dropIfExists('uspk_qc_verifications');
    }
};
