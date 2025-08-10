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
        Schema::create('block_inspection_teams', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('block_inspection_id');
            $table->unsignedMediumInteger('user_id');
            $table->string('role', 50)->default('Inspector');
            $table->boolean('is_lead')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('block_inspection_id')->references('id')->on('block_inspections')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('block_inspection_teams');
    }
};
