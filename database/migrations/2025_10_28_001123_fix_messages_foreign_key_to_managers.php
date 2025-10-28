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
        Schema::table('messages', function (Blueprint $table) {
            // Drop old foreign key
            $table->dropForeign(['sender_organization_manager_id']);
            
            // Add correct foreign key to managers table
            $table->foreign('sender_organization_manager_id')
                  ->references('id')
                  ->on('managers')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            // Drop the foreign key
            $table->dropForeign(['sender_organization_manager_id']);
            
            // Restore old foreign key (to organization_users)
            $table->foreign('sender_organization_manager_id')
                  ->references('id')
                  ->on('organization_users');
        });
    }
};
