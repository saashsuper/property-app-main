<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update issue status badge classes to use proper Bootstrap badge classes
        DB::table('issue_statuses')->where('value', 1)->update(['btn_class' => 'bg-warning']);
        DB::table('issue_statuses')->where('value', 2)->update(['btn_class' => 'bg-primary']);
        DB::table('issue_statuses')->where('value', 3)->update(['btn_class' => 'bg-secondary']);
        DB::table('issue_statuses')->where('value', 4)->update(['btn_class' => 'bg-success']);
        DB::table('issue_statuses')->where('value', 5)->update(['btn_class' => 'bg-light text-dark']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to button classes
        DB::table('issue_statuses')->where('value', 1)->update(['btn_class' => 'btn-warning']);
        DB::table('issue_statuses')->where('value', 2)->update(['btn_class' => 'btn-primary']);
        DB::table('issue_statuses')->where('value', 3)->update(['btn_class' => 'btn-secondary']);
        DB::table('issue_statuses')->where('value', 4)->update(['btn_class' => 'btn-success']);
        DB::table('issue_statuses')->where('value', 5)->update(['btn_class' => 'btn-light']);
    }
};
