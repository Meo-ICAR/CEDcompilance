<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Asset>
 */
class AssetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organizzazione_id' => \App\Models\Organizzazione::inRandomOrder()->first()?->id ?? 1,
            'nome' => $this->faker->word(),
            'categoria' => $this->faker->randomElement(['IT', 'Energia', 'Infrastruttura', 'Dati', 'Altro']),
            'descrizione' => $this->faker->sentence(),
            'ubicazione' => $this->faker->city(),
            'responsabile' => $this->faker->name(),
            'stato' => $this->faker->randomElement(['attivo', 'in manutenzione', 'disattivato']),
        ];
    }
}
