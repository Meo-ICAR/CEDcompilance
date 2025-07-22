<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class BackupSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\Backup::factory(30)->create();
    }
}
