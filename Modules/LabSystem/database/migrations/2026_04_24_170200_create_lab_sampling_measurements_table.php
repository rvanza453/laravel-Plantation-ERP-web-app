<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('lab_sampling_measurements')) {
            return;
        }

        Schema::create('lab_sampling_measurements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('lab_sampling_batch_id')->constrained('lab_sampling_batches')->cascadeOnDelete();
            $table->foreignId('lab_sampling_parameter_id')->constrained('lab_sampling_parameters')->cascadeOnDelete();
            $table->decimal('measured_value', 14, 4)->nullable();
            $table->string('analysis_method', 120)->nullable();
            $table->timestamp('measured_at')->nullable();
            $table->foreignId('analyst_user_id')->constrained('users')->restrictOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['lab_sampling_batch_id', 'lab_sampling_parameter_id'], 'lab_sampling_measurements_batch_param_uniq');
            $table->index(['measured_at', 'analyst_user_id'], 'lab_sampling_measurements_time_analyst_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_sampling_measurements');
    }
};
