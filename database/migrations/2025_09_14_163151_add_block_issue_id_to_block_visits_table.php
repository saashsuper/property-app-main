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
        Schema::table('block_visits', function (Blueprint $table) {
            $table->unsignedBigInteger('block_issue_id')->nullable()->after('block_id');
            $table->foreign('block_issue_id')->references('id')->on('block_issues')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('block_visits', function (Blueprint $table) {
            $table->dropForeign(['block_issue_id']);
            $table->dropColumn('block_issue_id');
        });
    }
};
