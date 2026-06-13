<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("UPDATE bookings SET source = 'walk_in' WHERE source = 'manual'");

        DB::statement("ALTER TABLE bookings MODIFY COLUMN source
            ENUM('website','whatsapp','walk_in') NOT NULL DEFAULT 'website'");
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("UPDATE bookings SET source = 'walk_in' WHERE source IN ('whatsapp','walk_in')");

        DB::statement("ALTER TABLE bookings MODIFY COLUMN source
            ENUM('website','manual') NOT NULL DEFAULT 'website'");
    }
};
