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
        Schema::create('work_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 20)->unique();
            $table->unsignedInteger('property_id');
            $table->unsignedInteger('contractor_id');
            $table->unsignedInteger('user_id');
            $table->unsignedTinyInteger('priority');
            $table->string('priority_label', 15);
            $table->text('fault_description');
            $table->string('issue_category', 1000);
            $table->string('issue_type', 1000);
            $table->string('issued_date', 20);
            $table->string('deadline', 10);
            $table->string('pricing', 5);
            $table->string('contact_name', 100)->nullable();
            $table->string('contact_number', 15)->nullable();
            $table->string('contact_email', 100)->nullable();
            $table->string('preferred_day', 10)->nullable();
            $table->string('time_from', 10)->nullable();
            $table->string('time_to', 10)->nullable();
            $table->string('note', 100)->nullable();
            $table->string('report', 200)->nullable();
            $table->string('type', 15);
            $table->unsignedInteger('type_id');
            $table->unsignedTinyInteger('common_status_id')->default(1);
            $table->mediumInteger('created_by')->nullable();
            $table->mediumInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_orders');
    }
};
