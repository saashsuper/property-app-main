<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * CONSOLIDATED MIGRATION - Combines:
     * - 2025_08_14_182533_create_block_information_table.php (initial table creation)
     * - 2025_09_27_143224_add_soft_deletes_to_block_information_table.php
     */
    public function up(): void
    {
        Schema::create('block_information', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('block_id');
            $table->unsignedBigInteger('information_type_id');
            $table->text('description');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('block_id')->references('id')->on('blocks')->onDelete('cascade');
            $table->foreign('information_type_id')->references('id')->on('block_information_types')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index('block_id');
            $table->index('information_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('block_information');
    }
};

