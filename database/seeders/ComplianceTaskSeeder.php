<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ComplianceTaskSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\ComplianceTask::factory(30)->create();
    }
}
