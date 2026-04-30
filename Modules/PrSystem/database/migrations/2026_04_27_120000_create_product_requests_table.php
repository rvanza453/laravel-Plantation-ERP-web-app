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
        Schema::create('product_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requester_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('decision_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('code', 50);
            $table->string('name');
            $table->string('unit', 50);
            $table->string('category', 100);
            $table->decimal('price_estimation', 15, 2)->default(0);
            $table->unsignedInteger('min_stock')->nullable();
            $table->string('reference_link', 2048);
            $table->string('status', 20)->default('Pending');
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['requester_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_requests');
    }
};