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
        Schema::create('block_work_order_teams', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('block_work_order_id');
            $table->unsignedBigInteger('user_id');
            $table->string('role', 50)->default('Contractor')->comment('Contractor, Admin');
            $table->boolean('is_lead')->default(false)->comment('Primary contractor or admin');
            $table->unsignedBigInteger('added_by')->nullable()->comment('Who added this team member');
            $table->softDeletes();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('block_work_order_id')->references('id')->on('block_work_orders')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('added_by')->references('id')->on('users')->onDelete('set null');
            
            // Indexes
            $table->index('block_work_order_id');
            $table->index('user_id');
            // Unique constraint: one user per work order
            $table->unique(['block_work_order_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('block_work_order_teams');
    }
};
