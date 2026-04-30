<?php

namespace Modules\LabSystem\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\LabSystem\Models\LabVerifierAssignment;
use App\Models\User;
use Modules\SystemISPO\Models\Site;

class LabVerifierAssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get QC users and first available site
        $qcUsers = User::role('quality control')->limit(3)->get();
        $firstSite = Site::first();

        if ($qcUsers->isEmpty()) {
            echo "❌ Tidak ada user dengan role 'quality control'. Setup user terlebih dahulu.\n";
            return;
        }

        // Create sample assignments
        $assignments = [];

        if ($qcUsers->count() >= 1) {
            // Global verifier
            $assignments[] = [
                'user_id' => $qcUsers[0]->id,
                'site_id' => null,
                'assignment_type' => 'global',
                'assignment_value' => null,
                'notes' => 'Verifikator utama untuk semua parameter',
                'is_active' => true,
            ];

            // Parameter-specific
            $assignments[] = [
                'user_id' => $qcUsers[0]->id,
                'site_id' => $firstSite?->id,
                'assignment_type' => 'parameter',
                'assignment_value' => 'FFA',
                'notes' => 'Spesialis untuk parameter FFA',
                'is_active' => true,
            ];
        }

        if ($qcUsers->count() >= 2) {
            $assignments[] = [
                'user_id' => $qcUsers[1]->id,
                'site_id' => $firstSite?->id,
                'assignment_type' => 'category',
                'assignment_value' => 'Kualitas',
                'notes' => 'Verifikator untuk kategori Kualitas',
                'is_active' => true,
            ];
        }

        if ($qcUsers->count() >= 3) {
            $assignments[] = [
                'user_id' => $qcUsers[2]->id,
                'site_id' => null,
                'assignment_type' => 'shift',
                'assignment_value' => '1',
                'notes' => 'Verifikator untuk Shift 1 (global)',
                'is_active' => true,
            ];
        }

        // Insert assignments
        foreach ($assignments as $assignment) {
            try {
                LabVerifierAssignment::create($assignment);
                echo "✅ Created assignment: {$assignment['assignment_type']} -> {$assignment['assignment_value']}\n";
            } catch (\Exception $e) {
                echo "⚠️  Skipped duplicate: {$assignment['assignment_type']} -> {$assignment['assignment_value']}\n";
            }
        }

        echo "\n✨ Lab Verifier Assignment seeding completed!\n";
    }
}
