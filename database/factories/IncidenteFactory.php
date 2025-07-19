<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Incidente>
 */
class IncidenteFactory extends Factory
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
                'Violazione dati',
                'Attacco ransomware',
                'Phishing riuscito',
                'Malware rilevato',
                'Interruzione servizio',
                'Accesso non autorizzato',
                'Perdita integrità dati',
            ]),
            'descrizione' => $this->faker->sentence(),
            'gravita' => $this->faker->randomElement(['bassa', 'media', 'alta']),
            'stato' => $this->faker->randomElement(['aperto', 'in analisi', 'risolto', 'chiuso']),
            'data_incidente' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'azioni_intrapesa' => $this->faker->randomElement([
                'Notifica al CSIRT',
                'Ripristino backup',
                'Blocco account compromessi',
                'Comunicazione interna',
                'Aggiornamento sistemi',
            ]),
        ];
    }
}
