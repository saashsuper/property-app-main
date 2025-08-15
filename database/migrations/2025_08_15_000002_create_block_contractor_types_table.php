<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('block_contractor_types', function (Blueprint $table) {
            $table->increments('id');
            $table->mediumText('document');
            $table->string('name', 30);
            $table->unsignedSmallInteger('common_status_id');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('block_contractor_types');
    }
};
