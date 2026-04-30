<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->syncUserForeignKey('lab_sampling_batches', 'sampler_user_id');
        $this->syncUserForeignKey('lab_sampling_measurements', 'analyst_user_id');
    }

    public function down(): void
    {
        if (Schema::hasTable('lab_sampling_batches') && Schema::hasColumn('lab_sampling_batches', 'sampler_user_id')) {
            $this->resetUserForeignKey('lab_sampling_batches', 'sampler_user_id');
        }

        if (Schema::hasTable('lab_sampling_measurements') && Schema::hasColumn('lab_sampling_measurements', 'analyst_user_id')) {
            $this->resetUserForeignKey('lab_sampling_measurements', 'analyst_user_id');
        }
    }

    private function syncUserForeignKey(string $tableName, string $columnName): void
    {
        if (!Schema::hasTable($tableName) || !Schema::hasColumn($tableName, $columnName)) {
            return;
        }

        $foreignKeyName = $tableName . '_' . $columnName . '_foreign';
        $hasForeignKey = DB::selectOne(
            'SELECT COUNT(*) AS total FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ? AND REFERENCED_TABLE_NAME = "users"',
            [$tableName, $columnName]
        );

        $nullCount = (int) DB::table($tableName)->whereNull($columnName)->count();

        if ((int) ($hasForeignKey->total ?? 0) > 0) {
            DB::statement(sprintf('ALTER TABLE `%s` DROP FOREIGN KEY `%s`', $tableName, $foreignKeyName));
        }

        if ($nullCount === 0) {
            DB::statement(sprintf('ALTER TABLE `%s` MODIFY `%s` BIGINT UNSIGNED NOT NULL', $tableName, $columnName));
        }

        DB::statement(sprintf(
            'ALTER TABLE `%s` ADD CONSTRAINT `%s` FOREIGN KEY (`%s`) REFERENCES `users` (`id`) ON DELETE RESTRICT',
            $tableName,
            $foreignKeyName,
            $columnName
        ));
    }

    private function resetUserForeignKey(string $tableName, string $columnName): void
    {
        $foreignKeyName = $tableName . '_' . $columnName . '_foreign';
        $hasForeignKey = DB::selectOne(
            'SELECT COUNT(*) AS total FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ? AND REFERENCED_TABLE_NAME = "users"',
            [$tableName, $columnName]
        );

        if ((int) ($hasForeignKey->total ?? 0) > 0) {
            DB::statement(sprintf('ALTER TABLE `%s` DROP FOREIGN KEY `%s`', $tableName, $foreignKeyName));
        }
        DB::statement(sprintf('ALTER TABLE `%s` MODIFY `%s` BIGINT UNSIGNED NULL', $tableName, $columnName));
        DB::statement(sprintf(
            'ALTER TABLE `%s` ADD CONSTRAINT `%s` FOREIGN KEY (`%s`) REFERENCES `users` (`id`) ON DELETE SET NULL',
            $tableName,
            $foreignKeyName,
            $columnName
        ));
    }
};