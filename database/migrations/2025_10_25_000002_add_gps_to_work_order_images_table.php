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
        Schema::table('block_work_order_images', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('image_name');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
        });

        Schema::table('block_inspection_asset_images', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('image_name');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
        });

        Schema::table('block_issue_images', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('image_name');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('block_work_order_images', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });

        Schema::table('block_inspection_asset_images', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });

        Schema::table('block_issue_images', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });
    }
};

