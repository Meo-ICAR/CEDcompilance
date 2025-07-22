<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TrainingEvent>
 */
class TrainingEventFactory extends Factory
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
            'topic' => $this->faker->randomElement([
                'Phishing Awareness',
                'Incident Response',
                'Data Protection',
                'Password Security',
                'Business Continuity',
            ]),
            'date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'completed' => $this->faker->boolean(90),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
