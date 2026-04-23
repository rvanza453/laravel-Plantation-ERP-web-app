<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('uspk_submissions', function (Blueprint $table) {
            $table->foreignId('uspk_bapp_id')->nullable()->after('job_id')->constrained('uspk_bapps')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('uspk_submissions', function (Blueprint $table) {
            $table->dropForeign(['uspk_bapp_id']);
            $table->dropColumn('uspk_bapp_id');
        });
    }
};
