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
        Schema::create('block_inspections', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('block_id');
            $table->string('ref_no', 30);
            $table->dateTime('scheduled_date_time');
            $table->dateTime('start_date_time')->nullable();
            $table->dateTime('end_date_time')->nullable();
            $table->string('notes', 255)->nullable();
            $table->string('pdf_path', 255)->nullable();
            $table->string('pdf_name', 100)->nullable();
            $table->unsignedSmallInteger('job_status_id')->default(1);
            $table->boolean('is_mobile')->default(false);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('block_id')->references('id')->on('blocks')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('block_inspections');
    }
};
