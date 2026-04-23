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
        Schema::create('uspk_bapps', function (Blueprint $table) {
            $table->id();
            $table->string('bapp_number')->unique();
            $table->date('bapp_date');
            $table->foreignId('job_id')->constrained();
            $table->foreignId('contractor_id')->constrained();
            $table->text('document_link')->nullable();
            $table->foreignId('uploaded_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uspk_bapps');
    }
};
