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
        Schema::create('blocks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('management_company', 100);
            $table->unsignedSmallInteger('block_type_id');
            $table->unsignedBigInteger('user_id');
            $table->string('address1', 100);
            $table->string('address2', 100)->nullable();
            $table->string('address3', 100)->nullable();
            $table->string('image_path', 255)->nullable();
            $table->string('image_name', 255)->nullable();
            $table->unsignedMediumInteger('country_id');
            $table->unsignedMediumInteger('state_id');
            $table->unsignedMediumInteger('car_spaces')->comment('No. of Car Spaces');
            $table->unsignedSmallInteger('inspection_count')->nullable();
            $table->unsignedSmallInteger('no_of_units')->nullable();
            $table->mediumInteger('created_by')->nullable();
            $table->mediumInteger('updated_by')->nullable();
            $table->mediumInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('block_type_id')->references('id')->on('block_types');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blocks');
    }
};
