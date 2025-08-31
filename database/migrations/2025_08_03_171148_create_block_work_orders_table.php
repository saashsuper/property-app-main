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
        Schema::create('block_work_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('block_id');
            $table->unsignedBigInteger('block_issue_id');
            $table->unsignedSmallInteger('issued_from')->nullable();
            $table->unsignedInteger('from_id')->nullable();
            $table->unsignedInteger('block_unit_id')->nullable();
            $table->unsignedBigInteger('block_building_id')->nullable();
            $table->unsignedSmallInteger('priority_id')->nullable();
            $table->timestamp('issued_date_time')->nullable();
            $table->unsignedInteger('contractor_id')->nullable();
            $table->string('contact_name', 100)->nullable();
            $table->string('contact_mobile', 20)->nullable();
            $table->string('contact_email', 100)->nullable();
            $table->dateTime('preferred_start_date_time')->nullable();
            $table->dateTime('preferred_end_date_time')->nullable();
            $table->date('deadline_date')->nullable();
            $table->unsignedBigInteger('issued_by');
            $table->unsignedSmallInteger('status');
            $table->string('ref_no', 100);
            $table->unsignedInteger('repair_category_id')->nullable();
            $table->string('issue', 255)->nullable();
            $table->string('note_for_access', 255)->nullable();
            $table->string('pdf_path', 255)->nullable();
            $table->string('pdf_name', 255)->nullable();
            $table->unsignedInteger('block_visit_id')->nullable();
            $table->unsignedInteger('block_inspection_id')->nullable();
            $table->mediumText('comment')->nullable();
            $table->boolean('is_mobile')->default(false);
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('block_id')->references('id')->on('blocks')->onDelete('cascade');
            $table->foreign('block_issue_id')->references('id')->on('block_issues')->onDelete('cascade');
            $table->foreign('block_unit_id')->references('id')->on('block_units')->onDelete('set null');
            $table->foreign('block_building_id')->references('id')->on('block_buildings')->onDelete('set null');
            $table->foreign('issued_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('block_work_orders');
    }
};
