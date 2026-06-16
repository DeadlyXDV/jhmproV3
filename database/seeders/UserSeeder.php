<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->superAdmin()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@jhmpro.test',
            'password' => Hash::make('password'),
        ]);

        User::factory()->admin()->create([
            'name' => 'Admin',
            'email' => 'admin@jhmpro.test',
            'password' => Hash::make('password'),
        ]);

        User::factory()->mekanik()->create([
            'name' => 'Mekanik',
            'email' => 'mekanik@jhmpro.test',
            'password' => Hash::make('password'),
        ]);
    }
}
