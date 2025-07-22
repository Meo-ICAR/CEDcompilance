<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notification>
 */
class NotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'incidente_id' => \App\Models\Incidente::inRandomOrder()->first()?->id ?? 1,
            'sent_to' => $this->faker->safeEmail(),
            'sent_at' => $this->faker->optional()->dateTimeBetween('-1 year', 'now'),
            'type' => $this->faker->randomElement(['CSIRT', 'DPA', 'Internal', 'Public']),
            'status' => $this->faker->randomElement(['pending', 'sent', 'failed']),
            'message' => $this->faker->sentence(),
        ];
    }
}
