<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Rischio>
 */
class RischioFactory extends Factory
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
            'titolo' => $this->faker->randomElement([
                'Accesso non autorizzato',
                'Perdita di dati',
                'Malware',
                'Phishing',
                'Furto di credenziali',
                'Interruzione servizio',
                'Vulnerabilità software',
                'Errore umano',
            ]),
            'descrizione' => $this->faker->sentence(),
            'probabilita' => $this->faker->randomElement(['bassa', 'media', 'alta']),
            'impatto' => $this->faker->randomElement(['basso', 'medio', 'alto']),
            'stato' => $this->faker->randomElement(['identificato', 'valutato', 'mitigato', 'accettato']),
            'azioni_mitigazione' => $this->faker->randomElement([
                'Implementazione firewall',
                'Backup regolari',
                'Formazione personale',
                'Aggiornamento software',
                'Controlli di accesso',
                'Monitoraggio continuo',
            ]),
            'data_valutazione' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
