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
        Schema::create('issue_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('block_issue_id');
            $table->string('log_type'); // e.g., 'work_order_created', 'site_visit_assigned', 'status_changed', 'contractor_assigned'
            $table->text('description'); // Human-readable description of what happened
            $table->string('field_name')->nullable(); // Name of field that changed (if applicable)
            $table->text('old_value')->nullable(); // Previous value (if applicable)
            $table->text('new_value')->nullable(); // New value (if applicable)
            $table->unsignedBigInteger('related_id')->nullable(); // ID of related entity (work_order_id, site_visit_id, etc.)
            $table->string('related_type')->nullable(); // Type of related entity (WorkOrder, SiteVisit, etc.)
            $table->unsignedBigInteger('user_id'); // User who made the change
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('block_issue_id')->references('id')->on('block_issues')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Index for faster queries
            $table->index(['block_issue_id', 'created_at']);
            $table->index('log_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issue_logs');
    }
};
