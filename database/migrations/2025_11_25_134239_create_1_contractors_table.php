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
        Schema::create('1_contractors', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement()->primary();
            $table->string('name', 100);
            $table->string('code', 100);
            $table->string('type', 100);
            $table->string('address_1', 100);
            $table->string('address_2', 100);
            $table->string('address_3', 100);
            $table->string('phone', 20);
            $table->string('mobile', 20);
            $table->string('emergency', 20);
            $table->string('email', 100);
            $table->string('secondary_email', 100);
            $table->string('work_order_email', 100);
            $table->string('main_contact', 100);
            $table->string('main_contact_email', 100);
            $table->string('main_contact_phone', 20);
            $table->string('accounts_contact', 100);
            $table->string('accounts_email', 100);
            $table->string('email_remittence', 100);
            $table->string('general_notes', 1000);
            $table->string('account_name', 255)->nullable();
            $table->string('bank', 255)->nullable();
            $table->string('bic', 255)->nullable();
            $table->string('iban', 255)->nullable();
            $table->string('pay_emts', 5)->nullable();
            $table->string('insurance_cover', 255)->nullable();
            $table->string('policy_number', 30)->nullable();
            $table->string('policy_expiry', 10)->nullable();
            $table->string('public_liablity_policy', 200)->nullable();
            $table->string('health_safety', 5)->nullable();
            $table->string('health_safety_statement', 200)->nullable();
            $table->unsignedTinyInteger('common_status_id');
            $table->mediumInteger('created_by')->nullable();
            $table->mediumInteger('updated_by')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('1_contractors');
    }
};
