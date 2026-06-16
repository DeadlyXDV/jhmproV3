<?php

namespace App\Console\Commands;

use App\Models\BookingSlot;
use App\Models\Setting;
use Illuminate\Console\Command;

class GenerateBookingSlots extends Command
{
    protected $signature = 'booking:generate-slots {--days= : Override advance days from settings}';

    protected $description = 'Generate booking slots for the next N days based on settings';

    public function handle(): int
    {
        $kapasitas = (int) Setting::get('booking_kapasitas', 2);
        $advanceDays = $this->option('days') !== null
            ? (int) $this->option('days')
            : (int) Setting::get('booking_advance_days', 30);
        $hariOperasional = json_decode(
            Setting::get('booking_hari', json_encode([1, 2, 3, 4, 5, 6])),
            true
        );

        $generated = 0;

        for ($i = 1; $i <= $advanceDays; $i++) {
            $date = now()->addDays($i);
            $isoDay = $date->isoWeekday();

            if (! in_array($isoDay, $hariOperasional)) {
                continue;
            }

            BookingSlot::updateOrCreate(
                ['tanggal' => $date->toDateString(), 'mekanik_id' => null],
                ['kapasitas' => $kapasitas]
            );

            $generated++;
        }

        $this->info("Generated/updated {$generated} booking slots.");

        return Command::SUCCESS;
    }
}
