<?php

namespace Modules\LabSystem\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class LabParameterSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('lab_sampling_parameters')) {
            return;
        }

        $rows = [
            // PROCESS CONTROL
            ['category' => 'PROCESS CONTROL', 'parameter_name' => 'Ffa Condensate', 'unit' => '%', 'standard_text' => '< 10', 'sampling_frequency' => 'Harian', 'is_calculated' => false],
            ['category' => 'PROCESS CONTROL', 'parameter_name' => 'Ffa Fat Fit', 'unit' => '%', 'standard_text' => '< 10', 'sampling_frequency' => 'Harian', 'is_calculated' => false],
            ['category' => 'PROCESS CONTROL', 'parameter_name' => 'Oil Underflow CST', 'unit' => '%', 'standard_text' => 'max 7', 'sampling_frequency' => 'Per 2 jam', 'is_calculated' => false],
            ['category' => 'PROCESS CONTROL', 'parameter_name' => 'Ligh phase Decanter', 'unit' => '%', 'standard_text' => '75.00', 'sampling_frequency' => 'Per 2 jam', 'is_calculated' => false],
            ['category' => 'PROCESS CONTROL', 'parameter_name' => 'Oil in Wet Basis Heavy Phase Decanter', 'unit' => '%', 'standard_text' => 'max 1,5', 'sampling_frequency' => 'Harian', 'is_calculated' => false],
            ['category' => 'PROCESS CONTROL', 'parameter_name' => 'Water Delution', 'unit' => '%', 'standard_text' => '15 - 20', 'sampling_frequency' => 'Per 2 jam', 'is_calculated' => false],
            ['category' => 'PROCESS CONTROL', 'parameter_name' => 'Temperatur CST', 'unit' => '°C', 'standard_text' => 'min 90', 'sampling_frequency' => 'Per 2 jam', 'is_calculated' => false],
            ['category' => 'PROCESS CONTROL', 'parameter_name' => 'Ripple Mill Effisiensy', 'unit' => '%', 'standard_text' => 'min 98', 'sampling_frequency' => 'Per 2 jam', 'is_calculated' => false],
            ['category' => 'PROCESS CONTROL', 'parameter_name' => 'Dencity larutan CaCO3', 'unit' => '-', 'standard_text' => '1 : 1,2', 'sampling_frequency' => 'Per 2 jam', 'is_calculated' => false],

            // FFA
            ['category' => 'FFA', 'parameter_name' => 'O Gutter', 'unit' => '%', 'standard_text' => '< 4', 'sampling_frequency' => 'Per 2 jam', 'is_calculated' => false],
            ['category' => 'FFA', 'parameter_name' => 'Cot', 'unit' => '%', 'standard_text' => '< 4', 'sampling_frequency' => 'Per 2 jam', 'is_calculated' => false],
            ['category' => 'FFA', 'parameter_name' => 'Oil CST', 'unit' => '%', 'standard_text' => '< 4', 'sampling_frequency' => 'Per 2 jam', 'is_calculated' => false],
            ['category' => 'FFA', 'parameter_name' => 'Oil Produksi', 'unit' => '%', 'standard_text' => '< 4,5', 'sampling_frequency' => 'Per 2 jam', 'is_calculated' => false],
            ['category' => 'FFA', 'parameter_name' => 'ost 1', 'unit' => '-', 'standard_text' => '-', 'sampling_frequency' => 'Harian', 'is_calculated' => false],
            ['category' => 'FFA', 'parameter_name' => 'ost 2', 'unit' => '-', 'standard_text' => '-', 'sampling_frequency' => 'Harian', 'is_calculated' => false],

            // Moisture
            ['category' => 'Moisture', 'parameter_name' => 'OT', 'unit' => '-', 'standard_text' => '-', 'sampling_frequency' => 'Per 2 jam', 'is_calculated' => false],
            ['category' => 'Moisture', 'parameter_name' => 'Produksi', 'unit' => '%', 'standard_text' => 'max 0.2', 'sampling_frequency' => 'Per 2 jam', 'is_calculated' => false],
            ['category' => 'Moisture', 'parameter_name' => 'ost 1', 'unit' => '-', 'standard_text' => '-', 'sampling_frequency' => 'Harian', 'is_calculated' => false],
            ['category' => 'Moisture', 'parameter_name' => 'ost 2', 'unit' => '-', 'standard_text' => '-', 'sampling_frequency' => 'Harian', 'is_calculated' => false],

            // Dirt
            ['category' => 'Dirt', 'parameter_name' => 'Produksi', 'unit' => '-', 'standard_text' => '-', 'sampling_frequency' => 'Harian', 'is_calculated' => false],
            ['category' => 'Dirt', 'parameter_name' => 'ost 1', 'unit' => '-', 'standard_text' => '-', 'sampling_frequency' => 'Harian', 'is_calculated' => false],
            ['category' => 'Dirt', 'parameter_name' => 'ost 2', 'unit' => '-', 'standard_text' => '-', 'sampling_frequency' => 'Harian', 'is_calculated' => false],

            // Oil Losses
            ['category' => 'Oil Losses', 'parameter_name' => 'Abs. Oil Losses in Fruit loss', 'unit' => '%', 'standard_text' => '0.09', 'sampling_frequency' => 'Harian', 'is_calculated' => false],
            ['category' => 'Oil Losses', 'parameter_name' => 'Abs. Oil in EFB ( Jankos)', 'unit' => '%', 'standard_text' => '0.33', 'sampling_frequency' => 'Harian', 'is_calculated' => false],
            ['category' => 'Oil Losses', 'parameter_name' => 'Abs. Oil Losses in Fibre', 'unit' => '%', 'standard_text' => '0.45', 'sampling_frequency' => 'Harian', 'is_calculated' => false],
            ['category' => 'Oil Losses', 'parameter_name' => 'Abs. Oil Losses in WetNut', 'unit' => '%', 'standard_text' => '0.05', 'sampling_frequency' => 'Harian', 'is_calculated' => false],
            ['category' => 'Oil Losses', 'parameter_name' => 'Abs. Final Effluent', 'unit' => '%', 'standard_text' => '0.3', 'sampling_frequency' => 'Harian', 'is_calculated' => false],
            ['category' => 'Oil Losses', 'parameter_name' => 'Abs. Solid Decanter', 'unit' => '%', 'standard_text' => '0.18', 'sampling_frequency' => 'Harian', 'is_calculated' => false],
            ['category' => 'Oil Losses', 'parameter_name' => 'Total Abs OIL LOSSES', 'unit' => '%', 'standard_text' => 'max 1.40', 'sampling_frequency' => 'Harian', 'is_calculated' => true],

            // Kernel Losses
            ['category' => 'Kernel Losses', 'parameter_name' => 'Abs. Cyclone Fibre Kernel Losses', 'unit' => '%', 'standard_text' => '0.16', 'sampling_frequency' => 'Harian', 'is_calculated' => false],
            ['category' => 'Kernel Losses', 'parameter_name' => 'Abs. Kernel LTDS I', 'unit' => '%', 'standard_text' => '0.05', 'sampling_frequency' => 'Harian', 'is_calculated' => false],
            ['category' => 'Kernel Losses', 'parameter_name' => 'Abs. Kernel LTDS II', 'unit' => '%', 'standard_text' => '0.02', 'sampling_frequency' => 'Harian', 'is_calculated' => false],
            ['category' => 'Kernel Losses', 'parameter_name' => 'Abs. Kernel Shell Ex. Hydr./Claybath', 'unit' => '%', 'standard_text' => '0.04', 'sampling_frequency' => 'Harian', 'is_calculated' => false],
            ['category' => 'Kernel Losses', 'parameter_name' => 'Total Abs KERNEL LOSSES', 'unit' => '%', 'standard_text' => 'max 0.27', 'sampling_frequency' => 'Harian', 'is_calculated' => true],
        ];

        $now = now();
        $hasCode = Schema::hasColumn('lab_sampling_parameters', 'parameter_code');
        $hasActive = Schema::hasColumn('lab_sampling_parameters', 'is_active');
        $hasDisplayOrder = Schema::hasColumn('lab_sampling_parameters', 'display_order');

        $prepared = [];
        foreach ($rows as $index => $row) {
            $payload = [
                'category' => $row['category'],
                'parameter_name' => $row['parameter_name'],
                'unit' => $row['unit'],
                'standard_text' => $row['standard_text'],
                'sampling_frequency' => $row['sampling_frequency'],
                'is_calculated' => (bool) $row['is_calculated'],
                'updated_at' => $now,
            ];

            if ($hasCode) {
                $payload['parameter_code'] = Str::upper(Str::substr(
                    Str::slug($row['category'] . '_' . $row['parameter_name'], '_'),
                    0,
                    40
                ));
            }

            if ($hasActive) {
                $payload['is_active'] = true;
            }

            if ($hasDisplayOrder) {
                $payload['display_order'] = ($index + 1) * 10;
            }

            $payload['created_at'] = $now;
            $prepared[] = $payload;
        }

        if ($hasCode) {
            $updateColumns = [
                'category',
                'parameter_name',
                'unit',
                'standard_text',
                'sampling_frequency',
                'is_calculated',
                'updated_at',
            ];

            if ($hasActive) {
                $updateColumns[] = 'is_active';
            }

            if ($hasDisplayOrder) {
                $updateColumns[] = 'display_order';
            }

            DB::table('lab_sampling_parameters')->upsert(
                $prepared,
                ['parameter_code'],
                $updateColumns
            );

            return;
        }

        DB::table('lab_sampling_parameters')->upsert(
            $prepared,
            ['category', 'parameter_name'],
            ['unit', 'standard_text', 'sampling_frequency', 'is_calculated', 'updated_at']
        );
    }
}
