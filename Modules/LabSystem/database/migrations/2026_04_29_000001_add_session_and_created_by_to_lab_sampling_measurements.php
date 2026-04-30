<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lab_sampling_measurements', function (Blueprint $table) {
            if (! Schema::hasColumn('lab_sampling_measurements', 'daily_session_id')) {
                $table->unsignedBigInteger('daily_session_id')->nullable()->after('batch_id');
                $table->index('daily_session_id');
            }

            if (! Schema::hasColumn('lab_sampling_measurements', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('daily_session_id');
                $table->index('created_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('lab_sampling_measurements', function (Blueprint $table) {
            if (Schema::hasColumn('lab_sampling_measurements', 'daily_session_id')) {
                $table->dropIndex(['daily_session_id']);
                $table->dropColumn('daily_session_id');
            }

            if (Schema::hasColumn('lab_sampling_measurements', 'created_by')) {
                $table->dropIndex(['created_by']);
                $table->dropColumn('created_by');
            }
        });
    }
};
