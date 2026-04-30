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
        Schema::create('lab_shift_sessions', function (Blueprint $table) {
            $table->id();
            $table->date('session_date');
            $table->integer('shift')->default(1); // 1 or 2
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('shift_start_at')->nullable();
            $table->dateTime('shift_end_at')->nullable();
            $table->enum('status', ['active', 'ended'])->default('active');
            
            // Mission tracking
            $table->integer('shift_missions_completed')->default(0);
            $table->integer('shift_missions_total')->default(0);
            $table->integer('daily_missions_completed')->default(0);
            $table->integer('daily_missions_total')->default(0);
            $table->boolean('daily_mission_completed_by_this_shift')->default(false);
            
            // Shared accountability
            $table->enum('daily_mission_status', ['pending', 'completed', 'failed'])->default('pending');
            $table->boolean('received_penalty')->default(false);
            $table->integer('final_score_percent')->default(0);
            $table->boolean('is_mvp')->default(false);
            
            // Data integrity
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['session_date', 'shift', 'status']);
            $table->index(['user_id', 'session_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_shift_sessions');
    }
};
