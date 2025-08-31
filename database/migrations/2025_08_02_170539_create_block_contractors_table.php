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
        Schema::create('block_contractors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('block_id');
            $table->unsignedInteger('contractor_type_id');
            $table->unsignedInteger('contractor_id');
            $table->unsignedTinyInteger('status')->default(0)->comment('1 for default');
            $table->mediumInteger('created_by')->nullable();
            $table->mediumInteger('updated_by')->nullable();
            $table->mediumInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('block_id')->references('id')->on('blocks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('block_contractors');
    }
};
