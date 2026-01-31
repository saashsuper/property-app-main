<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds assignment_type so contractor_id is unambiguous:
     * - outsource: contractor_id = contract_companies.id
     * - inhouse: contractor_id = users.id (property manager)
     */
    public function up(): void
    {
        Schema::table('block_work_orders', function (Blueprint $table) {
            $table->string('assignment_type', 20)->nullable()->after('contractor_id')
                ->comment('outsource = contract company, inhouse = property manager');
        });

        // Backfill: infer from existing contractor_id (users with Property manager type = inhouse, else outsource)
        $pmUserTypeId = DB::table('user_types')->where('name', 'Property manager')->value('id');
        if ($pmUserTypeId !== null) {
            $pmUserIds = DB::table('users')->where('user_type_id', $pmUserTypeId)->pluck('id');
            if ($pmUserIds->isNotEmpty()) {
                DB::table('block_work_orders')
                    ->whereNotNull('contractor_id')
                    ->whereIn('contractor_id', $pmUserIds)
                    ->update(['assignment_type' => 'inhouse']);
            }
        }
        DB::table('block_work_orders')
            ->whereNotNull('contractor_id')
            ->whereNull('assignment_type')
            ->update(['assignment_type' => 'outsource']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('block_work_orders', function (Blueprint $table) {
            $table->dropColumn('assignment_type');
        });
    }
};
