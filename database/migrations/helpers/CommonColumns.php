<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class {
    /**
     * Add common columns to a table.
     */
    public static function addCommonColumns(Blueprint $table): void
    {
        $table->timestamps();
        $table->softDeletes();
    }
};
