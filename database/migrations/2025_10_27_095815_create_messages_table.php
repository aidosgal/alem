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
        Schema::create('messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('chat_id');
            $table->text('content');
            $table->jsonb('metadata')->default('{}');
            $table->uuid('sender_applicant_id')->nullable();
            $table->uuid('sender_organization_manager_id')->nullable();
            $table->uuid('reply_to_message_id')->nullable();
            $table->timestamps();

            $table->foreign('chat_id')->references('id')->on('chats');
            $table->foreign('sender_applicant_id')->references('id')->on('applicants');
            $table->foreign('sender_organization_manager_id')->references('id')->on('organization_users');
            $table->foreign('reply_to_message_id')->references('id')->on('messages')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
