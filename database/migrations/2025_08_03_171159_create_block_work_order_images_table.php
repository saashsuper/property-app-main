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
        Schema::create('block_work_order_images', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('block_work_order_id');
            $table->string('image_name', 100)->nullable();
            $table->string('image_path', 255)->nullable();
            $table->string('strat_time', 255)->nullable();
            $table->unsignedSmallInteger('s3_status')->default(0);
            $table->softDeletes();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('block_work_order_id')->references('id')->on('block_work_orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('block_work_order_images');
    }
};
