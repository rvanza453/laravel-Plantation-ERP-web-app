<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('uspk_approvals')) {
            return;
        }

        Schema::table('uspk_approvals', function (Blueprint $table) {
            if (!Schema::hasColumn('uspk_approvals', 'vote_tender_id')) {
                $table->foreignId('vote_tender_id')
                    ->nullable()
                    ->constrained('uspk_tenders')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('uspk_approvals', 'vote_tender_value')) {
                $table->decimal('vote_tender_value', 15, 2)->nullable();
            }

            if (!Schema::hasColumn('uspk_approvals', 'vote_tender_duration')) {
                $table->integer('vote_tender_duration')->nullable();
            }

            if (!Schema::hasColumn('uspk_approvals', 'vote_tender_description')) {
                $table->text('vote_tender_description')->nullable();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('uspk_approvals')) {
            return;
        }

        Schema::table('uspk_approvals', function (Blueprint $table) {
            if (Schema::hasColumn('uspk_approvals', 'vote_tender_id')) {
                $table->dropConstrainedForeignId('vote_tender_id');
            }

            if (Schema::hasColumn('uspk_approvals', 'vote_tender_value')) {
                $table->dropColumn('vote_tender_value');
            }

            if (Schema::hasColumn('uspk_approvals', 'vote_tender_duration')) {
                $table->dropColumn('vote_tender_duration');
            }

            if (Schema::hasColumn('uspk_approvals', 'vote_tender_description')) {
                $table->dropColumn('vote_tender_description');
            }
        });
    }
};