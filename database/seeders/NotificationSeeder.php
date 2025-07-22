<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\Notification::factory(30)->create();
    }
}
