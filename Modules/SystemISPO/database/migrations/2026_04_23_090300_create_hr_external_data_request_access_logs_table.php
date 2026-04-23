<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_external_data_request_access_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('share_token_id')
                ->constrained('hr_external_data_request_share_tokens', 'id', 'hr_ext_req_log_token_fk')
                ->cascadeOnDelete();
            $table->foreignId('external_data_request_id')
                ->constrained('hr_external_data_requests', 'id', 'hr_ext_req_log_req_fk')
                ->cascadeOnDelete();
            $table->string('action', 30);
            $table->unsignedSmallInteger('status_code')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('accessed_at')->useCurrent();

            $table->index(['share_token_id', 'accessed_at'], 'hr_ext_req_access_token_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_external_data_request_access_logs');
    }
};
