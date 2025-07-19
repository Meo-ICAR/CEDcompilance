<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Organizzazione>
 */
class OrganizzazioneFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => $this->faker->company(),
            'partita_iva' => $this->faker->unique()->numerify('###########'),
            'indirizzo' => $this->faker->streetAddress(),
            'citta' => $this->faker->city(),
            'provincia' => $this->faker->lexify('??'),
            'cap' => $this->faker->numerify('#####'),
            'paese' => 'Italia',
            'referente' => $this->faker->name(),
            'email_referente' => $this->faker->unique()->safeEmail(),
            'telefono_referente' => $this->faker->phoneNumber(),
        ];
    }
}
