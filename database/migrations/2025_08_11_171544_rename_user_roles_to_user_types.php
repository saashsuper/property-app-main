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
        // Rename the table
        Schema::rename('user_roles', 'user_types');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rename the table back
        Schema::rename('user_types', 'user_roles');
    }
};
