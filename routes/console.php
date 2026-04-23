<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('hr:convert-legacy-ispo-roles {--dry-run : Preview conversion without updating data}', function () {
    $mapping = [
        'ISPO Admin' => 'HR Admin',
        'ISPO Auditor' => 'HR ISPO Auditor',
    ];

    $rows = DB::table('module_role_assignments')
        ->where('module_key', 'ispo')
        ->whereIn('role_name', array_keys($mapping))
        ->select('id', 'user_id', 'role_name')
        ->orderBy('id')
        ->get();

    if ($rows->isEmpty()) {
        $this->info('Tidak ada role legacy ISPO yang perlu dikonversi.');

        return self::SUCCESS;
    }

    $this->info('Ditemukan ' . $rows->count() . ' assignment role legacy untuk modul ispo.');
    $this->table(
        ['id', 'user_id', 'from_role', 'to_role'],
        $rows->map(fn ($row) => [
            'id' => $row->id,
            'user_id' => $row->user_id,
            'from_role' => $row->role_name,
            'to_role' => $mapping[$row->role_name] ?? $row->role_name,
        ])->all()
    );

    if ($this->option('dry-run')) {
        $this->warn('Dry-run aktif. Tidak ada data yang diubah.');

        return self::SUCCESS;
    }

    DB::transaction(function () use ($mapping): void {
        foreach ($mapping as $oldRole => $newRole) {
            DB::table('module_role_assignments')
                ->where('module_key', 'ispo')
                ->where('role_name', $oldRole)
                ->update([
                    'role_name' => $newRole,
                    'updated_at' => now(),
                ]);
        }
    });

    $this->info('Konversi role legacy selesai.');

    return self::SUCCESS;
})->purpose('One-time conversion: legacy ISPO roles to generalized HR roles');
