<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Backup>
 */
class BackupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'asset_id' => \App\Models\Asset::inRandomOrder()->first()?->id ?? 1,
            'backup_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'status' => $this->faker->randomElement(['ok', 'failed', 'in progress']),
            'tested_at' => $this->faker->optional()->dateTimeBetween('-1 year', 'now'),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
