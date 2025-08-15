<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('block_units', function (Blueprint $table) {
            $table->string('owners_name', 100)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('block_units', function (Blueprint $table) {
            $table->string('owners_name', 100)->nullable(false)->change();
        });
    }
};
