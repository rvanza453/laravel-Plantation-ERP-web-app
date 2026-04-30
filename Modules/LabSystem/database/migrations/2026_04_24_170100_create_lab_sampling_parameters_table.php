<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('lab_sampling_parameters')) {
            return;
        }

        Schema::create('lab_sampling_parameters', function (Blueprint $table): void {
            $table->id();
            $table->string('parameter_code', 40)->unique();
            $table->string('parameter_name', 120);
            $table->string('unit', 30)->default('UNIT');
            $table->decimal('target_min', 12, 4)->nullable();
            $table->decimal('target_max', 12, 4)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'parameter_name'], 'lab_sampling_parameters_active_name_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_sampling_parameters');
    }
};
