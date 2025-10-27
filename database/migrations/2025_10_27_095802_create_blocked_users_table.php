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
        Schema::create('blocked_users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('blocker_id');
            $table->uuid('blocked_user_id');
            $table->enum('blocked_user_type', ['applicant', 'organization_manager']);
            $table->timestamps();

            $table->foreign('blocker_id')->references('id')->on('applicants');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blocked_users');
    }
};
