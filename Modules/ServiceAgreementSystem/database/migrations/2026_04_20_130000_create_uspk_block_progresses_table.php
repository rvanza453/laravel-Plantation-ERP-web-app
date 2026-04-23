<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('uspk_block_progresses')) {
            Schema::create('uspk_block_progresses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('uspk_submission_id')->constrained('uspk_submissions')->cascadeOnDelete();
                $table->foreignId('block_id')->constrained('blocks')->cascadeOnDelete();
                $table->string('status', 20)->default('pending');
                $table->date('deadline_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->foreignId('completed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->unique(['uspk_submission_id', 'block_id']);
                $table->index(['uspk_submission_id', 'status']);
                $table->index('deadline_at');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('uspk_block_progresses');
    }
};
