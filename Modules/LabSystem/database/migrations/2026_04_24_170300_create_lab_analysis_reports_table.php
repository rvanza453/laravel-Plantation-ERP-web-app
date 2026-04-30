<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('lab_analysis_reports')) {
            return;
        }

        Schema::create('lab_analysis_reports', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('lab_sampling_batch_id')->constrained('lab_sampling_batches')->cascadeOnDelete();
            $table->string('report_number', 60)->unique();
            $table->enum('status', ['draft', 'reviewed', 'published'])->default('draft');
            $table->text('summary')->nullable();
            $table->text('recommendation')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->foreignId('prepared_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['status', 'published_at'], 'lab_analysis_reports_status_publish_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_analysis_reports');
    }
};
