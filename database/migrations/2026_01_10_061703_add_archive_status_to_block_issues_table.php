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
        Schema::table('block_issues', function (Blueprint $table) {
            $table->string('status', 20)->default('active')->after('issue_status_id');
            $table->mediumInteger('deleted_by')->nullable()->after('updated_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('block_issues', function (Blueprint $table) {
            $table->dropColumn(['status', 'deleted_by']);
        });
    }
};
