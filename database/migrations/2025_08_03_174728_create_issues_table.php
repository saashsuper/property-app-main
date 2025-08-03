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
        Schema::create('issues', function (Blueprint $table) {
            $table->id();
            $table->string('ref_no', 100)->unique();
            $table->string('title', 255);
            $table->text('description');
            $table->string('category', 100);
            $table->unsignedTinyInteger('priority')->default(2);
            $table->unsignedTinyInteger('status')->default(1);
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->unsignedBigInteger('reported_by');
            $table->string('location', 255)->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
            $table->foreign('reported_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issues');
    }
};
