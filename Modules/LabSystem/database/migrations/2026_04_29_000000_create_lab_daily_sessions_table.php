<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('lab_daily_sessions')) {
            Schema::create('lab_daily_sessions', function (Blueprint $table) {
                $table->id();
                $table->date('session_date')->unique();
                $table->enum('status', ['open', 'pending_verification', 'verified'])->default('open');
                $table->unsignedBigInteger('started_by')->nullable();
                $table->timestamp('started_at')->nullable();
                $table->unsignedBigInteger('closed_by')->nullable();
                $table->timestamp('closed_at')->nullable();
                $table->unsignedBigInteger('verifier_id')->nullable();
                $table->timestamp('verified_at')->nullable();
                $table->text('verifier_notes')->nullable();
                $table->timestamps();

                $table->index('session_date');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_daily_sessions');
    }
};
