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
        Schema::create('block_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('block_id')->constrained('blocks')->onDelete('cascade');
            $table->string('original_name'); // Original filename
            $table->string('stored_name'); // Generated filename for storage
            $table->string('file_path'); // Path where file is stored
            $table->string('file_extension'); // File extension
            $table->bigInteger('file_size'); // File size in bytes
            $table->string('mime_type'); // MIME type
            $table->integer('sort_order')->default(0); // For ordering images
            $table->boolean('is_primary')->default(false); // Primary image flag
            $table->text('description')->nullable(); // Optional image description
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['block_id', 'sort_order']);
            $table->index(['block_id', 'is_primary']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('block_images');
    }
};