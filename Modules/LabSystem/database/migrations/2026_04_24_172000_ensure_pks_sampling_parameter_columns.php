<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('lab_sampling_parameters')) {
            Schema::create('lab_sampling_parameters', function (Blueprint $table): void {
                $table->id();
                $table->string('category');
                $table->string('parameter_name');
                $table->string('unit')->nullable();
                $table->string('standard_text')->nullable();
                $table->string('sampling_frequency');
                $table->boolean('is_calculated')->default(false);
                $table->timestamps();
            });

            return;
        }

        Schema::table('lab_sampling_parameters', function (Blueprint $table): void {
            if (!Schema::hasColumn('lab_sampling_parameters', 'category')) {
                $table->string('category')->nullable()->after('id');
            }

            if (!Schema::hasColumn('lab_sampling_parameters', 'parameter_name')) {
                $table->string('parameter_name')->after('category');
            }

            if (!Schema::hasColumn('lab_sampling_parameters', 'unit')) {
                $table->string('unit')->nullable()->after('parameter_name');
            }

            if (!Schema::hasColumn('lab_sampling_parameters', 'standard_text')) {
                $table->string('standard_text')->nullable()->after('unit');
            }

            if (!Schema::hasColumn('lab_sampling_parameters', 'sampling_frequency')) {
                $table->string('sampling_frequency')->nullable()->after('standard_text');
            }

            if (!Schema::hasColumn('lab_sampling_parameters', 'is_calculated')) {
                $table->boolean('is_calculated')->default(false)->after('sampling_frequency');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('lab_sampling_parameters')) {
            return;
        }

        Schema::table('lab_sampling_parameters', function (Blueprint $table): void {
            if (Schema::hasColumn('lab_sampling_parameters', 'is_calculated')) {
                $table->dropColumn('is_calculated');
            }
        });
    }
};
