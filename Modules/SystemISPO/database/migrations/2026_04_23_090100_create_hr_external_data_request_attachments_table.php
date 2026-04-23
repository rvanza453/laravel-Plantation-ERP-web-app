<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_external_data_request_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('external_data_request_id')
                ->constrained('hr_external_data_requests', 'id', 'hr_ext_req_att_req_fk')
                ->cascadeOnDelete();
            $table->enum('kategori_lampiran', ['incoming_document', 'outgoing_document']);
            $table->string('file_name');
            $table->string('file_path');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->default(0);
            $table->foreignId('uploaded_by')->nullable()->constrained('users', 'id', 'hr_ext_req_att_up_fk')->nullOnDelete();
            $table->timestamp('uploaded_at')->useCurrent();
            $table->timestamps();

            $table->index(['external_data_request_id', 'kategori_lampiran'], 'hr_ext_req_attachment_cat_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_external_data_request_attachments');
    }
};
