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
        Schema::create('work_order_images', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('work_order_id');
            $table->string('image', 200);
            $table->unsignedInteger('common_status_id')->default(1);
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('work_order_id')->references('id')->on('work_orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_order_images');
    }
};
