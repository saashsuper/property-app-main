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
            $table->unsignedBigInteger('contact_method_id')->nullable()->after('contact_email');
            $table->foreign('contact_method_id')->references('id')->on('contact_methods')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('block_issues', function (Blueprint $table) {
            $table->dropForeign(['contact_method_id']);
            $table->dropColumn('contact_method_id');
        });
    }
};
