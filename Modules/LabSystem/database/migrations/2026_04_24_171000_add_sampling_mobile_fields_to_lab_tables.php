<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('lab_sampling_batches')) {
            Schema::table('lab_sampling_batches', function (Blueprint $table): void {
                if (!Schema::hasColumn('lab_sampling_batches', 'shift')) {
                    $table->unsignedTinyInteger('shift')->default(1)->after('sampler_user_id');
                }

                if (!Schema::hasColumn('lab_sampling_batches', 'submitted_at')) {
                    $table->timestamp('submitted_at')->nullable()->after('status');
                }
            });
        }

        if (Schema::hasTable('lab_sampling_parameters')) {
            Schema::table('lab_sampling_parameters', function (Blueprint $table): void {
                if (!Schema::hasColumn('lab_sampling_parameters', 'category')) {
                    $table->string('category', 80)->nullable()->after('parameter_name');
                }

                if (!Schema::hasColumn('lab_sampling_parameters', 'standard_text')) {
                    $table->string('standard_text', 120)->nullable()->after('unit');
                }

                if (!Schema::hasColumn('lab_sampling_parameters', 'sampling_frequency')) {
                    $table->string('sampling_frequency', 60)->nullable()->after('standard_text');
                }

                if (!Schema::hasColumn('lab_sampling_parameters', 'display_order')) {
                    $table->unsignedInteger('display_order')->default(0)->after('sampling_frequency');
                }
            });
        }

        if (Schema::hasTable('lab_sampling_measurements')) {
            Schema::table('lab_sampling_measurements', function (Blueprint $table): void {
                if (!Schema::hasColumn('lab_sampling_measurements', 'measured_text')) {
                    $table->string('measured_text', 50)->nullable()->after('measured_value');
                }
            });
        }

        if (Schema::hasTable('lab_sampling_parameters')) {
            $parameters = [
                ['parameter_code' => 'PC_FFA_CONDENSATE', 'parameter_name' => 'Ffa Condensate', 'category' => 'Process Control', 'unit' => '( % )', 'standard_text' => '< 10', 'sampling_frequency' => 'Harian', 'display_order' => 10],
                ['parameter_code' => 'PC_FFA_FIT', 'parameter_name' => 'Ffa Fat Fit', 'category' => 'Process Control', 'unit' => '( % )', 'standard_text' => '< 10', 'sampling_frequency' => 'Harian', 'display_order' => 20],
                ['parameter_code' => 'PC_OIL_UNDERFLOW_CST', 'parameter_name' => 'Oil Underflow CST', 'category' => 'Process Control', 'unit' => '( % )', 'standard_text' => 'max 7', 'sampling_frequency' => 'Per 2 jam', 'display_order' => 30],
                ['parameter_code' => 'PC_LIGHT_PHASE_DECANTER', 'parameter_name' => 'Ligh phase Decanter', 'category' => 'Process Control', 'unit' => '( % )', 'standard_text' => '75.00', 'sampling_frequency' => 'Per 2 jam', 'display_order' => 40],
                ['parameter_code' => 'PC_OIL_WET_BASIS', 'parameter_name' => 'Oil in Wet Basis Heavy Phase Decanter', 'category' => 'Process Control', 'unit' => '( % )', 'standard_text' => 'max 1,5', 'sampling_frequency' => 'Harian', 'display_order' => 50],
                ['parameter_code' => 'PC_WATER_DILUTION', 'parameter_name' => 'Water Delution (Per 2 Jam)', 'category' => 'Process Control', 'unit' => '( % )', 'standard_text' => '15 - 20', 'sampling_frequency' => 'Per 2 jam', 'display_order' => 60],
                ['parameter_code' => 'PC_TEMP_CST', 'parameter_name' => 'Temperatur CST', 'category' => 'Process Control', 'unit' => '°C', 'standard_text' => 'min 90', 'sampling_frequency' => 'Per 2 jam', 'display_order' => 70],
                ['parameter_code' => 'PC_RIPPLE_MILL', 'parameter_name' => 'Ripple Mill Effisiensy', 'category' => 'Process Control', 'unit' => '( % )', 'standard_text' => 'min 98', 'sampling_frequency' => 'Per 2 jam', 'display_order' => 80],
                ['parameter_code' => 'PC_DENSITY_CACO3', 'parameter_name' => 'Dencity larutan CaCO3', 'category' => 'Process Control', 'unit' => '-', 'standard_text' => '1 : 1,2', 'sampling_frequency' => 'Per 2 jam', 'display_order' => 90],

                ['parameter_code' => 'FFA_O_GUTTER', 'parameter_name' => 'O Gutter', 'category' => 'FFA', 'unit' => '( % )', 'standard_text' => '< 4', 'sampling_frequency' => 'Per 2 jam', 'display_order' => 110],
                ['parameter_code' => 'FFA_COT', 'parameter_name' => 'Cot', 'category' => 'FFA', 'unit' => '( % )', 'standard_text' => '< 4', 'sampling_frequency' => 'Per 2 jam', 'display_order' => 120],
                ['parameter_code' => 'FFA_OIL_CST', 'parameter_name' => 'Oil CST', 'category' => 'FFA', 'unit' => '( % )', 'standard_text' => '< 4', 'sampling_frequency' => 'Per 2 jam', 'display_order' => 130],
                ['parameter_code' => 'FFA_OIL_PRODUKSI', 'parameter_name' => 'Oil Produksi', 'category' => 'FFA', 'unit' => '( % )', 'standard_text' => '< 4,5', 'sampling_frequency' => 'Per 2 jam', 'display_order' => 140],

                ['parameter_code' => 'MOISTURE_OT', 'parameter_name' => 'OT', 'category' => 'Moisture', 'unit' => '-', 'standard_text' => '-', 'sampling_frequency' => 'Per 2 jam', 'display_order' => 210],
                ['parameter_code' => 'MOISTURE_PRODUKSI', 'parameter_name' => 'Produksi', 'category' => 'Moisture', 'unit' => '( % )', 'standard_text' => 'max 0.2', 'sampling_frequency' => 'Per 2 jam', 'display_order' => 220],

                ['parameter_code' => 'DIRT_PRODUKSI', 'parameter_name' => 'Produksi', 'category' => 'Dirt', 'unit' => '-', 'standard_text' => '-', 'sampling_frequency' => 'Harian', 'display_order' => 310],

                ['parameter_code' => 'LOSS_FRUIT', 'parameter_name' => 'Abs. Oil Losses in Fruit loss', 'category' => 'Oil Losses', 'unit' => '( % )', 'standard_text' => '0.09', 'sampling_frequency' => 'Harian', 'display_order' => 410],
                ['parameter_code' => 'LOSS_EFB', 'parameter_name' => 'Abs. Oil in EFB (Jankos)', 'category' => 'Oil Losses', 'unit' => '( % )', 'standard_text' => '0.33', 'sampling_frequency' => 'Harian', 'display_order' => 420],
                ['parameter_code' => 'LOSS_FIBRE', 'parameter_name' => 'Abs. Oil Losses in Fibre', 'category' => 'Oil Losses', 'unit' => '( % )', 'standard_text' => '0.45', 'sampling_frequency' => 'Harian', 'display_order' => 430],
                ['parameter_code' => 'LOSS_WETNUT', 'parameter_name' => 'Abs. Oil Losses in WetNut', 'category' => 'Oil Losses', 'unit' => '( % )', 'standard_text' => '0.05', 'sampling_frequency' => 'Harian', 'display_order' => 440],
                ['parameter_code' => 'LOSS_FINAL_EFFLUENT', 'parameter_name' => 'Abs. Final Effluent', 'category' => 'Oil Losses', 'unit' => '( % )', 'standard_text' => '0.3', 'sampling_frequency' => 'Harian', 'display_order' => 450],
                ['parameter_code' => 'LOSS_SOLID_DECANTER', 'parameter_name' => 'Abs. Solid Decanter', 'category' => 'Oil Losses', 'unit' => '( % )', 'standard_text' => '0.18', 'sampling_frequency' => 'Harian', 'display_order' => 460],
                ['parameter_code' => 'LOSS_TOTAL_OIL', 'parameter_name' => 'Total Abs OIL LOSSES', 'category' => 'Oil Losses', 'unit' => '( % )', 'standard_text' => 'max 1.40', 'sampling_frequency' => 'Harian', 'display_order' => 470],

                ['parameter_code' => 'KERNEL_CYCLONE_FIBRE', 'parameter_name' => 'Abs. Cyclone Fibre Kernel Losses', 'category' => 'Kernel Losses', 'unit' => '( % )', 'standard_text' => '0.16', 'sampling_frequency' => 'Harian', 'display_order' => 510],
                ['parameter_code' => 'KERNEL_LTDS_I', 'parameter_name' => 'Abs. Kernel LTDS I', 'category' => 'Kernel Losses', 'unit' => '( % )', 'standard_text' => '0.05', 'sampling_frequency' => 'Harian', 'display_order' => 520],
                ['parameter_code' => 'KERNEL_LTDS_II', 'parameter_name' => 'Abs. Kernel LTDS II', 'category' => 'Kernel Losses', 'unit' => '( % )', 'standard_text' => '0.02', 'sampling_frequency' => 'Harian', 'display_order' => 530],
                ['parameter_code' => 'KERNEL_SHELL_HYDRO', 'parameter_name' => 'Abs. Kernel Shell Ex. Hydr./Claybath', 'category' => 'Kernel Losses', 'unit' => '( % )', 'standard_text' => '0.04', 'sampling_frequency' => 'Harian', 'display_order' => 540],
                ['parameter_code' => 'KERNEL_TOTAL_LOSS', 'parameter_name' => 'Total Abs KERNEL LOSSES', 'category' => 'Kernel Losses', 'unit' => '( % )', 'standard_text' => 'max 0.27', 'sampling_frequency' => 'Harian', 'display_order' => 550],
            ];

            foreach ($parameters as $parameter) {
                $exists = DB::table('lab_sampling_parameters')
                    ->where('parameter_code', $parameter['parameter_code'])
                    ->exists();

                if ($exists) {
                    DB::table('lab_sampling_parameters')
                        ->where('parameter_code', $parameter['parameter_code'])
                        ->update([
                            'parameter_name' => $parameter['parameter_name'],
                            'category' => $parameter['category'],
                            'unit' => $parameter['unit'],
                            'standard_text' => $parameter['standard_text'],
                            'sampling_frequency' => $parameter['sampling_frequency'],
                            'display_order' => $parameter['display_order'],
                            'is_active' => true,
                            'updated_at' => now(),
                        ]);
                } else {
                    DB::table('lab_sampling_parameters')->insert([
                        'parameter_code' => $parameter['parameter_code'],
                        'parameter_name' => $parameter['parameter_name'],
                        'category' => $parameter['category'],
                        'unit' => $parameter['unit'],
                        'standard_text' => $parameter['standard_text'],
                        'sampling_frequency' => $parameter['sampling_frequency'],
                        'display_order' => $parameter['display_order'],
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('lab_sampling_measurements') && Schema::hasColumn('lab_sampling_measurements', 'measured_text')) {
            Schema::table('lab_sampling_measurements', function (Blueprint $table): void {
                $table->dropColumn('measured_text');
            });
        }

        if (Schema::hasTable('lab_sampling_parameters')) {
            Schema::table('lab_sampling_parameters', function (Blueprint $table): void {
                if (Schema::hasColumn('lab_sampling_parameters', 'display_order')) {
                    $table->dropColumn('display_order');
                }
                if (Schema::hasColumn('lab_sampling_parameters', 'sampling_frequency')) {
                    $table->dropColumn('sampling_frequency');
                }
                if (Schema::hasColumn('lab_sampling_parameters', 'standard_text')) {
                    $table->dropColumn('standard_text');
                }
                if (Schema::hasColumn('lab_sampling_parameters', 'category')) {
                    $table->dropColumn('category');
                }
            });
        }

        if (Schema::hasTable('lab_sampling_batches')) {
            Schema::table('lab_sampling_batches', function (Blueprint $table): void {
                if (Schema::hasColumn('lab_sampling_batches', 'submitted_at')) {
                    $table->dropColumn('submitted_at');
                }
                if (Schema::hasColumn('lab_sampling_batches', 'shift')) {
                    $table->dropColumn('shift');
                }
            });
        }
    }
};
