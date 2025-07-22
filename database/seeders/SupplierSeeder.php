<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\Supplier::factory(15)->create();
    }
}
