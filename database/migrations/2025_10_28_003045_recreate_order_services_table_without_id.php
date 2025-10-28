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
        // Drop existing table
        Schema::dropIfExists('order_services');
        
        // Recreate without id field
        Schema::create('order_services', function (Blueprint $table) {
            $table->uuid('order_id');
            $table->uuid('service_id');
            $table->string('status')->default('pending_funds');
            $table->timestamps();

            $table->primary(['order_id', 'service_id']);
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_services');
        
        // Restore old structure
        Schema::create('order_services', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('order_id');
            $table->uuid('service_id');
            $table->string('status')->default('pending_funds');
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('service_id')->references('id')->on('services');
        });
    }
};
