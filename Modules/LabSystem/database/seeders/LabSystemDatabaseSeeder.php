<?php

namespace Modules\LabSystem\Database\Seeders;

use Illuminate\Database\Seeder;

class LabSystemDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            LabParameterSeeder::class,
        ]);
    }
}
