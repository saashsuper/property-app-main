<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('block_visits', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('block_id');
            $table->string('ref_no', 30);
            $table->dateTime('scheduled_date_time');
            $table->dateTime('start_date_time')->nullable();
            $table->dateTime('end_date_time')->nullable();
            $table->unsignedSmallInteger('job_reason_id')->nullable();
            $table->string('notes', 255)->nullable();
            $table->mediumText('comment')->nullable();
            $table->string('pdf_path', 255)->nullable();
            $table->string('pdf_name', 100)->nullable();
            $table->unsignedSmallInteger('job_status_id')->nullable();
            $table->unsignedSmallInteger('block_visit_action_id')->nullable();
            $table->boolean('is_mobile')->default(false);
            $table->mediumInteger('created_by')->nullable();
            $table->mediumInteger('updated_by')->nullable();
            $table->mediumInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('block_visit_images', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('block_visit_id');
            $table->string('image_path', 255)->nullable();
            $table->unsignedSmallInteger('s3_status')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('block_visit_results', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('block_visit_id');
            $table->unsignedInteger('block_unit_id');
            $table->unsignedSmallInteger('value');
            $table->string('notes', 255);
            $table->mediumInteger('created_by')->nullable();
            $table->mediumInteger('updated_by')->nullable();
            $table->mediumInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('block_visit_teams', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('block_visit_id');
            $table->unsignedInteger('user_id');
            $table->boolean('leed')->default(false);
            $table->mediumInteger('created_by')->nullable();
            $table->mediumInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('block_visit_teams');
        Schema::dropIfExists('block_visit_results');
        Schema::dropIfExists('block_visit_images');
        Schema::dropIfExists('block_visits');
    }
};


