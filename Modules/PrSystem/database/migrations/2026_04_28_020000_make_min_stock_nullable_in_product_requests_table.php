<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('product_requests') && Schema::hasColumn('product_requests', 'min_stock')) {
            DB::statement('ALTER TABLE product_requests MODIFY min_stock INT UNSIGNED NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('product_requests') && Schema::hasColumn('product_requests', 'min_stock')) {
            DB::statement('ALTER TABLE product_requests MODIFY min_stock INT UNSIGNED NOT NULL DEFAULT 0');
        }
    }
};
