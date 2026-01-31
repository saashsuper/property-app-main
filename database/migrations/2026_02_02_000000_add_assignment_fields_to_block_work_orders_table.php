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
        Schema::table('block_work_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('contract_company_id')->nullable()->after('contractor_id');
            $table->unsignedBigInteger('property_manager_id')->nullable()->after('contract_company_id');

            $table->foreign('contract_company_id')
                ->references('id')
                ->on('contract_companies')
                ->onDelete('set null');

            $table->foreign('property_manager_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('block_work_orders', function (Blueprint $table) {
            $table->dropForeign(['contract_company_id']);
            $table->dropForeign(['property_manager_id']);
            $table->dropColumn(['contract_company_id', 'property_manager_id']);
        });
    }
};
