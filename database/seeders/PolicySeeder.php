<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PolicySeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\Policy::factory(10)->create();
    }
}
