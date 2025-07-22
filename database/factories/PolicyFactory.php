<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Policy>
 */
class PolicyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->randomElement([
                'Information Security Policy',
                'Incident Response Policy',
                'Data Retention Policy',
                'Access Control Policy',
            ]),
            'version' => $this->faker->randomElement(['1.0', '1.1', '2.0', '2.1']),
            'description' => $this->faker->paragraph(),
            'file_path' => $this->faker->randomElement([
                'policies/security.pdf',
                'policies/incident_response.pdf',
                'policies/data_retention.pdf',
            ]),
            'effective_date' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'reviewed_at' => $this->faker->optional()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
