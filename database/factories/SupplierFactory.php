<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Supplier>
 */
class SupplierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'contact' => $this->faker->email(),
            'risk_level' => $this->faker->randomElement(['low', 'medium', 'high']),
            'description' => $this->faker->sentence(),
            'compliance_status' => $this->faker->randomElement(['unknown', 'compliant', 'non-compliant']),
        ];
    }
}
