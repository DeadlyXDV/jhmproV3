<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite' || ! Schema::hasTable('bookings')) {
            return;
        }

        DB::statement("ALTER TABLE bookings MODIFY COLUMN status
            ENUM('pending','confirmed','in_progress','completed','cancelled')
            NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("UPDATE bookings SET status = 'completed' WHERE status = 'in_progress'");

        DB::statement("ALTER TABLE bookings MODIFY COLUMN status
            ENUM('pending','confirmed','cancelled','completed')
            NOT NULL DEFAULT 'pending'");
    }
};
