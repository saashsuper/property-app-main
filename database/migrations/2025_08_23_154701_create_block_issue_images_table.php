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
        Schema::create('block_issue_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('block_issue_id');
            $table->string('image_path');
            $table->string('image_name');
            $table->boolean('s3_status')->default(false);
            $table->timestamps();
            
            // Add foreign key constraint for block_issue_id
            $table->foreign('block_issue_id')->references('id')->on('block_issues')->onDelete('cascade');
            
            // Add index for better performance
            $table->index('block_issue_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('block_issue_images');
    }
};
