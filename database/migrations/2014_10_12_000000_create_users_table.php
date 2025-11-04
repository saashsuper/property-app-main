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
     * - 2014_10_12_000000_create_users_table.php (initial table creation)
     * - 2025_09_05_042851_add_created_by_to_users_table.php
     * - 2025_10_04_153227_add_updated_by_deleted_by_to_users_table.php
     * - 2025_10_22_183025_add_profile_fields_to_users_table.php
     * - 2025_10_25_000001_add_fcm_token_to_users_table.php
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->unsignedBigInteger('user_type_id')->nullable();
            $table->string('avatar')->nullable();
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->string('fcm_token')->nullable();
            
            // Audit fields
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            
            // Common columns
            require_once __DIR__.'/helpers/CommonColumns.php';
            $commonColumns = require __DIR__.'/helpers/CommonColumns.php';
            $commonColumns->addCommonColumns($table);
            
            // Foreign key constraints
            $table->foreign('user_type_id')->references('id')->on('user_types')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('deleted_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

