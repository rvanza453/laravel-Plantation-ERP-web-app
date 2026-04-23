<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_external_data_request_share_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('external_data_request_id')
                ->constrained('hr_external_data_requests', 'id', 'hr_ext_req_tok_req_fk')
                ->cascadeOnDelete();
            $table->string('token_hash', 64)->unique();
            $table->string('token_hint')->nullable();
            $table->boolean('allow_download')->default(true);
            $table->boolean('allow_preview_only')->default(false);
            $table->unsignedInteger('max_views')->nullable();
            $table->unsignedInteger('view_count')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('last_accessed_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users', 'id', 'hr_ext_req_tok_user_fk')->nullOnDelete();
            $table->timestamps();

            $table->index(['external_data_request_id', 'revoked_at'], 'hr_ext_req_token_revoked_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_external_data_request_share_tokens');
    }
};
