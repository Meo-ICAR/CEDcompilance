<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TrainingEventSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\TrainingEvent::factory(30)->create();
    }
}
