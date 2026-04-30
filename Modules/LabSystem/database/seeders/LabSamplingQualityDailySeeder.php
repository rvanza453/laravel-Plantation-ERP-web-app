<?php

namespace Modules\LabSystem\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Exception;

class LabSamplingQualityDailySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Pastikan tabel yang dibutuhkan ada
        if (!Schema::hasTable('lab_sampling_batches') || !Schema::hasTable('lab_sampling_measurements') || !Schema::hasTable('lab_sampling_parameters')) {
            $this->command->error('Missing required lab sampling tables.');
            return;
        }

        $userIds = DB::table('users')->pluck('id')->toArray();
        if (empty($userIds)) {
            $this->command->error('No users found in database to act as sampler/verifier.');
            return;
        }

        $samplerId = $userIds[0];
        $verifierId = $userIds[array_rand($userIds)];
        
        $today = Carbon::today();
        $sourceUnit = 'PKS SSM';

        $parameters = DB::table('lab_sampling_parameters')->get();
        if ($parameters->isEmpty()) {
            $this->command->error('No parameters found. Run LabParameterSeeder first.');
            return;
        }

        // Generate 8 batches untuk hari ini (4 di Shift 1, 4 di Shift 2)
        $batchCount = 8;
        
        for ($i = 0; $i < $batchCount; $i++) {
            $shift = ($i < 4) ? 1 : 2;
            
            // Random time during the shift
            $baseHour = ($shift === 1) ? 7 : 19;
            $hour = $baseHour + ($i % 4) * 2; // e.g. 7, 9, 11, 13
            $samplingTime = $today->copy()->setHour($hour)->setMinute(rand(0, 59));
            
            $batchCode = 'LAB-DUMMY-' . $today->format('Ymd') . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT);
            
            $batchId = DB::table('lab_sampling_batches')->insertGetId([
                'batch_code' => $batchCode,
                'sampling_date' => $today->toDateString(),
                'source_unit' => $sourceUnit,
                'sampler_user_id' => $samplerId,
                'status' => 'approved', // Langsung approved agar masuk ke Quality Report
                'notes' => 'Dummy Data Hasil Inspeksi Seeder',
                'shift' => $shift,
                'verified_by' => $verifierId,
                'verified_at' => $samplingTime->copy()->addMinutes(30),
                'verifier_notes' => 'Telah diperiksa - Valid via Seeder',
                'submitted_at' => $samplingTime,
                'created_at' => $samplingTime,
                'updated_at' => $samplingTime->copy()->addMinutes(30),
            ]);

            // Untuk setiap parameter buat measurement yang realistis
            $measurements = [];
            foreach ($parameters as $parameter) {
                // Generate realistic values based on category
                $value = $this->generateRealisticValue($parameter->category, $parameter->parameter_name);
                
                $measurements[] = [
                    'lab_sampling_batch_id' => $batchId,
                    'lab_sampling_parameter_id' => $parameter->id,
                    'measured_value' => $value,
                    'measured_text' => (string) $value,
                    'analysis_method' => 'sampling_input_mobile',
                    'measured_at' => $samplingTime,
                    'analyst_user_id' => $samplerId,
                    'created_at' => $samplingTime,
                    'updated_at' => $samplingTime,
                ];
            }
            
            DB::table('lab_sampling_measurements')->insert($measurements);
        }
        
        $this->command->info('Successfully seeded 8 sampling batches for today with realistic Quality Daily data.');
    }

    private function generateRealisticValue($category, $paramName)
    {
        $category = strtolower($category);
        $paramName = strtolower($paramName);

        // Nilai target dan batas normal
        if ($category === 'ffa' || str_contains($paramName, 'ffa')) {
            // Target < 4.5% generally, we generate between 3.5 and 5.0
            return round(3.5 + (rand(0, 150) / 100), 2); 
        }
        
        if (str_contains($category, 'losses') || str_contains($paramName, 'losses')) {
            // Target < 5%, mostly generates between 1 and 6
            return round(1.0 + (rand(0, 500) / 100), 2);
        }

        if ($category === 'moisture') {
            // Target is 30-40% typically, let's put it around 25 to 45
            return round(25 + rand(0, 200) / 10, 2);
        }

        if ($category === 'dirt') {
            // Dirt percentage, e.g. 0.05 to 0.4
            return round(0.05 + rand(0, 35) / 100, 2);
        }
        
        if ($category === 'process control') {
            if (str_contains($paramName, 'temperatur')) {
                // > 90 C
                return rand(88, 98);
            }
            if (str_contains($paramName, 'water')) {
                // 15 - 20
                return round(14 + rand(0, 80) / 10, 2);
            }
            return round(10 + rand(0, 500) / 10, 2); // Default varying value
        }

        // Generic default for other unknown parameters
        return round(rand(10, 1000) / 10, 2); 
    }
}
