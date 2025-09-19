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
        Schema::create('block_issue_actions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('block_issue_id');
            $table->string('action_type'); // e.g., 'inspection', 'repair', 'follow_up', 'resolved', 'escalated'
            $table->text('description');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('performed_by'); // user who performed the action
            $table->timestamp('action_date');
            $table->string('status')->default('completed'); // 'pending', 'in_progress', 'completed', 'cancelled'
            $table->decimal('cost', 10, 2)->nullable(); // optional cost associated with the action
            $table->string('priority')->nullable(); // 'low', 'normal', 'high', 'urgent', 'critical'
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('block_issue_id')->references('id')->on('block_issues')->onDelete('cascade');
            $table->foreign('performed_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('block_issue_actions');
    }
};
