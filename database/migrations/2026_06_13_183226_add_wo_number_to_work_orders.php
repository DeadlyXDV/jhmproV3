<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->string('wo_number')->nullable()->unique()->after('id');
        });

        // Generate wo_number untuk data lama yang sudah ada
        $workOrders = DB::table('work_orders')->orderBy('id')->get();
        foreach ($workOrders as $i => $wo) {
            $number = 'WO-'.str_pad($i + 1, 4, '0', STR_PAD_LEFT);
            DB::table('work_orders')->where('id', $wo->id)->update(['wo_number' => $number]);
        }

        Schema::table('work_orders', function (Blueprint $table) {
            $table->string('wo_number')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropColumn('wo_number');
        });
    }
};
