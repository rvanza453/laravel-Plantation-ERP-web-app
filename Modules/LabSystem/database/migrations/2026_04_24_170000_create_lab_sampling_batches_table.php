<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('lab_sampling_batches')) {
            return;
        }

        Schema::create('lab_sampling_batches', function (Blueprint $table): void {
            $table->id();
            $table->string('batch_code', 50)->unique();
            $table->date('sampling_date');
            $table->string('source_unit', 100)->nullable();
            $table->string('estate_code', 50)->nullable();
            $table->string('block_code', 50)->nullable();
            $table->string('sample_type', 50)->nullable();
            $table->foreignId('sampler_user_id')->constrained('users')->restrictOnDelete();
            $table->enum('status', ['draft', 'sampled', 'in_analysis', 'completed'])->default('draft');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['sampling_date', 'status'], 'lab_sampling_batches_date_status_idx');
            $table->index(['source_unit', 'estate_code'], 'lab_sampling_batches_unit_estate_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_sampling_batches');
    }
};
