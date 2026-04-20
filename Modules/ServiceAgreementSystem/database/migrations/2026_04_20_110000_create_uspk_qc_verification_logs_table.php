<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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

    public function down(): void
    {
        Schema::dropIfExists('uspk_qc_verification_logs');
    }
};
