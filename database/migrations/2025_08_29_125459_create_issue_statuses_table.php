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
        Schema::create('issue_statuses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('label', 50);
            $table->unsignedSmallInteger('value')->default(1);
            $table->string('btn_class', 50)->nullable();
            $table->string('description', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issue_statuses');
    }
};
