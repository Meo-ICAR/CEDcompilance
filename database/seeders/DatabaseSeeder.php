<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seeder utenti di esempio
        \App\Models\User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Seeder compliance NIS2
        $this->call([
            OrganizzazioneSeeder::class,
            AssetSeeder::class,
            RischioSeeder::class,
            IncidenteSeeder::class,
            Nis2Seeder::class,
        ]);
    }
}
