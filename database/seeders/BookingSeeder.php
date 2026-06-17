<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\BookingService;
use App\Models\Customer;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $customers = Customer::with('vehicles')->get();
        $mekanik = User::where('role', 'mekanik')->first();
        $tuneUpSvc = Service::where('nama_service', 'Tune Up Standar')->first();
        $overhaul = Service::where('nama_service', 'Overhaul Mesin')->first();
        $boreUp = Service::where('nama_service', 'Bore Up 125cc ke 150cc')->first();

        static $counter = 0;
        $nextBkNumber = function () use (&$counter): string {
            $counter++;

            return 'BK-'.now()->year.'-'.str_pad($counter, 4, '0', STR_PAD_LEFT);
        };

        $bookings = [
            // Pending — akan datang
            [
                'customer' => $customers->get(0),
                'status' => 'pending',
                'tanggal_booking' => now()->addDays(3)->format('Y-m-d'),
                'jam_mulai' => '09:00', 'jam_selesai' => '10:00',
                'source' => 'website',
                'keluhan' => 'Motor tarikan berat dan boros bensin sejak 2 minggu lalu',
                'service' => $tuneUpSvc,
            ],
            [
                'customer' => $customers->get(4),
                'status' => 'pending',
                'tanggal_booking' => now()->addDays(5)->format('Y-m-d'),
                'jam_mulai' => '13:00', 'jam_selesai' => '16:00',
                'source' => 'whatsapp',
                'keluhan' => 'Mau bore up motor, pengen lebih kencang buat harian',
                'service' => $boreUp,
            ],
            [
                'customer' => $customers->get(7),
                'status' => 'pending',
                'tanggal_booking' => now()->addDays(7)->format('Y-m-d'),
                'jam_mulai' => '10:00', 'jam_selesai' => '11:00',
                'source' => 'website',
                'keluhan' => 'Ganti oli rutin + cek keseluruhan',
                'service' => $tuneUpSvc,
            ],

            // Confirmed
            [
                'customer' => $customers->get(1),
                'status' => 'confirmed',
                'tanggal_booking' => now()->addDays(1)->format('Y-m-d'),
                'jam_mulai' => '08:00', 'jam_selesai' => '09:30',
                'source' => 'website',
                'keluhan' => 'Service rutin 5000km',
                'service' => $tuneUpSvc,
                'confirmed_at' => now()->subHours(2),
                'catatan_admin' => 'Customer baru pertama kali, kasih welcome bonus diskon 10%',
            ],
            [
                'customer' => $customers->get(3),
                'status' => 'confirmed',
                'tanggal_booking' => now()->addDays(2)->format('Y-m-d'),
                'jam_mulai' => '14:00', 'jam_selesai' => '16:00',
                'source' => 'whatsapp',
                'keluhan' => 'Overhaul mesin Ninja ZX25R, sudah 30.000km belum pernah overhaul',
                'service' => $overhaul,
                'confirmed_at' => now()->subDay(),
            ],

            // In Progress (sedang dikerjakan hari ini)
            [
                'customer' => $customers->get(2),
                'status' => 'in_progress',
                'tanggal_booking' => now()->format('Y-m-d'),
                'jam_mulai' => '09:00', 'jam_selesai' => '10:00',
                'source' => 'walk_in',
                'keluhan' => 'Rem blong dan bunyi saat diinjak',
                'service' => $tuneUpSvc,
                'confirmed_at' => now()->subHours(3),
            ],

            // Completed
            [
                'customer' => $customers->get(5),
                'status' => 'completed',
                'tanggal_booking' => now()->subDays(5)->format('Y-m-d'),
                'jam_mulai' => '10:00', 'jam_selesai' => '11:00',
                'source' => 'website',
                'keluhan' => 'Tune up rutin',
                'service' => $tuneUpSvc,
                'confirmed_at' => now()->subDays(6),
            ],
            [
                'customer' => $customers->get(8),
                'status' => 'completed',
                'tanggal_booking' => now()->subDays(10)->format('Y-m-d'),
                'jam_mulai' => '13:00', 'jam_selesai' => '16:00',
                'source' => 'whatsapp',
                'keluhan' => 'Pasang knalpot R9 dan tune up',
                'service' => $tuneUpSvc,
                'confirmed_at' => now()->subDays(11),
            ],
            [
                'customer' => $customers->get(10),
                'status' => 'completed',
                'tanggal_booking' => now()->subDays(15)->format('Y-m-d'),
                'jam_mulai' => '08:00', 'jam_selesai' => '12:00',
                'source' => 'website',
                'keluhan' => 'Bore up dan porting polish',
                'service' => $boreUp,
                'confirmed_at' => now()->subDays(16),
            ],

            // Cancelled
            [
                'customer' => $customers->get(6),
                'status' => 'cancelled',
                'tanggal_booking' => now()->subDays(3)->format('Y-m-d'),
                'jam_mulai' => '09:00', 'jam_selesai' => '10:00',
                'source' => 'website',
                'keluhan' => 'Tune up ringan',
                'service' => $tuneUpSvc,
                'confirmed_at' => now()->subDays(4),
                'cancelled_at' => now()->subDays(4)->addHours(5),
                'cancel_reason' => 'Customer tidak jadi, ada keperluan mendadak',
            ],
        ];

        foreach ($bookings as $data) {
            /** @var Customer $customer */
            $customer = $data['customer'];
            if (! $customer) {
                continue;
            }

            $vehicle = $customer->vehicles->first();

            /** @var Booking $booking */
            $booking = Booking::create([
                'customer_id' => $customer->id,
                'vehicle_id' => $vehicle?->id,
                'mekanik_id' => $mekanik?->id,
                'booking_number' => $nextBkNumber(),
                'tanggal_booking' => $data['tanggal_booking'],
                'jam_mulai' => $data['jam_mulai'],
                'jam_selesai' => $data['jam_selesai'],
                'status' => $data['status'],
                'source' => $data['source'],
                'keluhan' => $data['keluhan'] ?? null,
                'catatan_admin' => $data['catatan_admin'] ?? null,
                'nama_pemesan' => $customer->nama,
                'no_hp_pemesan' => $customer->no_hp,
                'confirmed_at' => $data['confirmed_at'] ?? null,
                'cancelled_at' => $data['cancelled_at'] ?? null,
                'cancel_reason' => $data['cancel_reason'] ?? null,
            ]);

            if ($data['service']) {
                BookingService::create([
                    'booking_id' => $booking->id,
                    'service_id' => $data['service']->id,
                    'bundle_id' => null,
                    'type' => 'service',
                    'nama_snapshot' => $data['service']->nama_service,
                    'harga_estimasi' => $data['service']->harga_default,
                ]);
            }
        }
    }
}
