<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ComplianceTask>
 */
class ComplianceTaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'due_date' => $this->faker->dateTimeBetween('now', '+6 months'),
            'completed_at' => $this->faker->optional()->dateTimeBetween('-1 year', 'now'),
            'assigned_to' => \App\Models\User::inRandomOrder()->first()?->id ?? 1,
            'status' => $this->faker->randomElement(['open', 'in progress', 'completed', 'overdue']),
            'related_model_type' => $this->faker->randomElement([
                'App\\Models\\Asset',
                'App\\Models\\Incidente',
                'App\\Models\\Rischio',
            ]),
            'related_model_id' => 1, // You may want to set this in the seeder for more realism
        ];
    }
}
