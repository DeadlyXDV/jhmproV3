<?php

namespace Database\Seeders;

use App\Models\SparepartCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SparepartCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Oli & Pelumas', 'description' => 'Semua jenis oli dan pelumas kendaraan', 'children' => [
                ['name' => 'Oli Mesin', 'description' => 'Oli untuk pelumasan mesin 4-tak'],
                ['name' => 'Oli Gardan', 'description' => 'Oli transmisi/gardan untuk matic'],
                ['name' => 'Oli Rem', 'description' => 'Minyak rem hidrolik'],
            ]],
            ['name' => 'Mesin & Transmisi', 'description' => 'Komponen internal mesin dan transmisi', 'children' => [
                ['name' => 'Piston & Blok Silinder', 'description' => 'Piston, ring piston, blok silinder, boring'],
                ['name' => 'Klep & Noken As', 'description' => 'Klep isap, klep buang, noken as racing'],
                ['name' => 'Kopling', 'description' => 'Kampas kopling, per kopling, plat kopling'],
                ['name' => 'Rantai & Gir', 'description' => 'Rantai keteng, gir depan, gir belakang'],
            ]],
            ['name' => 'Rem & Suspensi', 'description' => 'Sistem pengereman dan suspensi', 'children' => [
                ['name' => 'Kampas Rem', 'description' => 'Kampas rem cakram dan tromol'],
                ['name' => 'Cakram Rem', 'description' => 'Piringan cakram rem depan dan belakang'],
                ['name' => 'Shockbreaker', 'description' => 'Suspensi depan dan belakang'],
            ]],
            ['name' => 'Pengapian & Kelistrikan', 'description' => 'Sistem pengapian dan kelistrikan', 'children' => [
                ['name' => 'Busi', 'description' => 'Busi standar, iridium, racing'],
                ['name' => 'CDI & ECU', 'description' => 'CDI racing, ECU programmable'],
                ['name' => 'Aki & Kelistrikan', 'description' => 'Aki, koil, regulator, kabel bodi'],
            ]],
            ['name' => 'Filter & Saringan', 'description' => 'Semua jenis filter kendaraan', 'children' => [
                ['name' => 'Filter Udara', 'description' => 'Filter udara OEM dan racing'],
                ['name' => 'Filter Oli', 'description' => 'Saringan oli mesin'],
            ]],
            ['name' => 'Knalpot & Exhaust', 'description' => 'Knalpot racing dan aksesoris exhaust'],
            ['name' => 'Aksesoris & Modifikasi', 'description' => 'Aksesoris tampilan dan modifikasi'],
        ];

        foreach ($categories as $catData) {
            $children = $catData['children'] ?? [];
            unset($catData['children']);

            $parent = SparepartCategory::create([
                'name' => $catData['name'],
                'slug' => Str::slug($catData['name']),
                'description' => $catData['description'] ?? null,
                'parent_id' => null,
            ]);

            foreach ($children as $child) {
                SparepartCategory::create([
                    'name' => $child['name'],
                    'slug' => Str::slug($child['name']),
                    'description' => $child['description'] ?? null,
                    'parent_id' => $parent->id,
                ]);
            }
        }
    }
}
