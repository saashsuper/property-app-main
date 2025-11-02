<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds additional_comments field to block_inspection_assets table
     * Used for Commercial Business Park and Houses building types to store separate comments
     */
    public function up(): void
    {
        Schema::table('block_inspection_assets', function (Blueprint $table) {
            $table->text('additional_comments')->nullable()->after('comments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('block_inspection_assets', function (Blueprint $table) {
            $table->dropColumn('additional_comments');
        });
    }
};
