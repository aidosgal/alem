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
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('applicant_id');
            $table->uuid('organization_id');
            $table->text('title');
            $table->text('description')->nullable();
            $table->decimal('price', 15, 2)->nullable();
            $table->timestampTz('deadline_at');
            $table->string('status')->nullable();
            $table->timestamps();

            $table->foreign('applicant_id')->references('id')->on('applicants');
            $table->foreign('organization_id')->references('id')->on('organizations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
