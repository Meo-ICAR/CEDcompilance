<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AuditLog>
 */
class AuditLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::inRandomOrder()->first()?->id ?? 1,
            'action' => $this->faker->randomElement(['create', 'update', 'delete', 'login', 'logout']),
            'auditable_type' => $this->faker->randomElement([
                'App\\Models\\Asset',
                'App\\Models\\Incidente',
                'App\\Models\\Rischio',
                'App\\Models\\User',
            ]),
            'auditable_id' => 1, // You may want to set this in the seeder for more realism
            'old_values' => json_encode(['field' => 'old']),
            'new_values' => json_encode(['field' => 'new']),
            'ip_address' => $this->faker->ipv4(),
        ];
    }
}
