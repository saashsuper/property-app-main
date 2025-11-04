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
     * - 2014_10_12_000000_create_user_types_table.php (initial table creation)
     * - 2025_09_04_000001_add_is_hidden_to_user_types_table.php
     */
    public function up(): void
    {
        Schema::create('user_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->boolean('is_hidden')->default(false);
            require_once __DIR__.'/helpers/CommonColumns.php';
            $commonColumns = require __DIR__.'/helpers/CommonColumns.php';
            $commonColumns->addCommonColumns($table);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_types');
    }
};

