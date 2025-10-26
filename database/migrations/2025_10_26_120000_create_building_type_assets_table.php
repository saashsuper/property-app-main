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
        Schema::create('building_type_assets', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedSmallInteger('building_type_id');
            $table->unsignedSmallInteger('building_asset_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('building_type_assets');
    }
};

