<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->unverified()->create([
            'name' => 'Usuario 1',
            'email' => 'usuario1@example.com',
        ]);

        User::factory()->unverified()->create([
            'name' => 'Usuario 2',
            'email' => 'usuario2@example.com',
        ]);
    }
}
