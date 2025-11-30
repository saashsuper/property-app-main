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
        Schema::create('block_work_order_notes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('block_work_order_id');
            $table->text('note');
            $table->string('note_type', 50)->default('note')->comment('Type of note: note, pause_reason, etc.');
            $table->unsignedBigInteger('created_by');
            $table->softDeletes();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('block_work_order_id')->references('id')->on('block_work_orders')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            
            // Indexes
            $table->index('block_work_order_id');
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('block_work_order_notes');
    }
};
