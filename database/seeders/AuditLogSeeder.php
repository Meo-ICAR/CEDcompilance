<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AuditLogSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\AuditLog::factory(50)->create();
    }
}
