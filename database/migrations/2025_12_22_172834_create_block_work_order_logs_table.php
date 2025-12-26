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
        Schema::create('block_work_order_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('block_work_order_id');
            $table->string('log_type'); // e.g., 'created', 'status_changed', 'priority_changed', 'assigned', 'completed', 'paused', 'resumed'
            $table->text('description'); // Human-readable description of what happened
            $table->string('field_name')->nullable(); // Name of field that changed (if applicable)
            $table->text('old_value')->nullable(); // Previous value (if applicable)
            $table->text('new_value')->nullable(); // New value (if applicable)
            $table->unsignedBigInteger('related_id')->nullable(); // ID of related entity
            $table->string('related_type')->nullable(); // Type of related entity
            $table->unsignedBigInteger('user_id'); // User who made the change
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('block_work_order_id')->references('id')->on('block_work_orders')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Index for faster queries
            $table->index(['block_work_order_id', 'created_at']);
            $table->index('log_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('block_work_order_logs');
    }
};
