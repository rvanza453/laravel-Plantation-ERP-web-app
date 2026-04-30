<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('ispo_documents')) {
            return;
        }

        $targetTable = Schema::hasTable('sites') ? 'sites' : 'ispo_sites';

        $fkExists = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', 'ispo_documents')
            ->where('COLUMN_NAME', 'site_id')
            ->whereNotNull('REFERENCED_TABLE_NAME')
            ->exists();

        if ($fkExists) {
            Schema::table('ispo_documents', function (Blueprint $table) {
                $table->dropForeign(['site_id']);
            });
        }

        Schema::table('ispo_documents', function (Blueprint $table) use ($targetTable) {
            $table->foreign('site_id')->references('id')->on($targetTable)->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('ispo_documents', function (Blueprint $table) {
            $table->dropForeign(['site_id']);
            $table->foreign('site_id')->references('id')->on('ispo_sites')->onDelete('cascade');
        });
    }
};
