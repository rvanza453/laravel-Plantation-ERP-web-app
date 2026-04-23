<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('uspk_bapps') || !Schema::hasColumn('uspk_bapps', 'contractor_id')) {
            return;
        }

        $fkName = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', DB::raw('DATABASE()'))
            ->where('TABLE_NAME', 'uspk_bapps')
            ->where('COLUMN_NAME', 'contractor_id')
            ->whereNotNull('REFERENCED_TABLE_NAME')
            ->value('CONSTRAINT_NAME');

        if ($fkName) {
            DB::statement("ALTER TABLE uspk_bapps DROP FOREIGN KEY `{$fkName}`");
        }

        DB::statement('ALTER TABLE uspk_bapps MODIFY contractor_id BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE uspk_bapps ADD CONSTRAINT uspk_bapps_contractor_id_foreign FOREIGN KEY (contractor_id) REFERENCES contractors(id) ON DELETE RESTRICT');
    }

    public function down(): void
    {
        if (!Schema::hasTable('uspk_bapps') || !Schema::hasColumn('uspk_bapps', 'contractor_id')) {
            return;
        }

        $hasNullRows = DB::table('uspk_bapps')->whereNull('contractor_id')->exists();
        if ($hasNullRows) {
            return;
        }

        $fkName = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', DB::raw('DATABASE()'))
            ->where('TABLE_NAME', 'uspk_bapps')
            ->where('COLUMN_NAME', 'contractor_id')
            ->whereNotNull('REFERENCED_TABLE_NAME')
            ->value('CONSTRAINT_NAME');

        if ($fkName) {
            DB::statement("ALTER TABLE uspk_bapps DROP FOREIGN KEY `{$fkName}`");
        }

        DB::statement('ALTER TABLE uspk_bapps MODIFY contractor_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE uspk_bapps ADD CONSTRAINT uspk_bapps_contractor_id_foreign FOREIGN KEY (contractor_id) REFERENCES contractors(id) ON DELETE RESTRICT');
    }
};
