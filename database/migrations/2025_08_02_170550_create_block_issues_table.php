                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * CONSOLIDATED MIGRATION - Combines:
     * - 2025_08_02_170550_create_block_issues_table.php (initial table creation)
     * - 2025_08_23_085213_add_assigned_to_to_block_issues_table.php
     */
    public function up(): void
    {
        Schema::create('block_issues', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('ref_no', 100);
            $table->unsignedInteger('block_id');
            $table->unsignedSmallInteger('issued_from')->nullable();
            $table->unsignedInteger('from_id')->nullable();
            $table->unsignedSmallInteger('contractor_type_id')->nullable();
            $table->unsignedSmallInteger('priority_id')->default(1);
            $table->unsignedInteger('block_unit_id')->nullable();
            $table->string('contact_details', 255)->nullable();
            $table->string('issue', 255)->nullable();
            $table->string('issue_type')->nullable();
            $table->text('issue_details')->nullable();
            $table->string('contact_name', 100)->nullable();
            $table->string('salutation', 10)->nullable();
            $table->string('contact_mobile', 20)->nullable();
            $table->string('phone_number', 20)->nullable();
            $table->string('contact_email', 100)->nullable();
            $table->unsignedBigInteger('contact_method_id')->nullable();
            $table->dateTime('preferred_start_date_time')->nullable();
            $table->dateTime('preferred_end_date_time')->nullable();
            $table->string('note_for_access', 255)->nullable();
            $table->unsignedBigInteger('issued_by');
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->unsignedInteger('block_visit_id')->nullable();
            $table->unsignedInteger('block_inspection_id')->nullable();
            $table->dateTime('issued_date_time')->nullable();
            $table->mediumText('comment')->nullable();
            $table->boolean('is_mobile')->default(false);
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->unsignedInteger('issue_status_id')->default(1);
            $table->unsignedBigInteger('reported_by');
            $table->softDeletes();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('block_id')->references('id')->on('blocks')->onDelete('cascade');
            $table->foreign('block_unit_id')->references('id')->on('block_units')->onDelete('cascade');
            $table->foreign('contact_method_id')->references('id')->on('contact_methods')->onDelete('set null');
            $table->foreign('issued_by')->references('id')->on('users');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('reported_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('block_issues');
    }
};

