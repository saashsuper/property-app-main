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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->unsignedBigInteger('user_role_id')->nullable();
            $table->string('avatar')->nullable();
            $table->rememberToken();
            require_once __DIR__.'/helpers/CommonColumns.php';
            $commonColumns = require __DIR__.'/helpers/CommonColumns.php';
            $commonColumns->addCommonColumns($table);
            // Foreign key will be added after user_roles table is created
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
