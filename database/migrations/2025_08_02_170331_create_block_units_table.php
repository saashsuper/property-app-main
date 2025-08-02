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
        Schema::create('block_units', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('block_id');
            $table->unsignedBigInteger('block_building_id');
            $table->unsignedSmallInteger('block_unit_type_id');
            $table->string('unit_code', 50);
            $table->string('unit_name', 100);
            $table->string('owners_name', 100);
            $table->string('salutation', 10);
            $table->string('email', 100);
            $table->boolean('resident');
            $table->string('address1', 100)->nullable();
            $table->string('address2', 100)->nullable();
            $table->string('address3', 100)->nullable();
            $table->unsignedMediumInteger('country_id');
            $table->unsignedMediumInteger('state_id');
            $table->string('zip', 30);
            $table->string('mobile_no', 20)->nullable();
            $table->string('phone_number', 20)->nullable();
            $table->string('letting_agent', 100)->nullable();
            $table->string('misc_info', 255)->nullable();
            $table->mediumInteger('created_by')->nullable();
            $table->mediumInteger('updated_by')->nullable();
            $table->mediumInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('block_id')->references('id')->on('blocks')->onDelete('cascade');
            $table->foreign('block_building_id')->references('id')->on('block_buildings')->onDelete('cascade');
            $table->foreign('block_unit_type_id')->references('id')->on('block_unit_types');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('block_units');
    }
};
