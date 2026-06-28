<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite' || ! Schema::hasTable('invoices')) {
            return;
        }

        DB::statement("ALTER TABLE invoices MODIFY COLUMN payment_status
            ENUM('unpaid','partial','paid','voided')
            NOT NULL DEFAULT 'unpaid'");
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("UPDATE invoices SET payment_status = 'unpaid' WHERE payment_status = 'voided'");

        DB::statement("ALTER TABLE invoices MODIFY COLUMN payment_status
            ENUM('unpaid','partial','paid')
            NOT NULL DEFAULT 'unpaid'");
    }
};
