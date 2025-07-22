<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class IncidenteSeeder extends Seeder
{
    public function run(): void
    {
        $scenarios = [
            ['titolo' => 'Violazione dati', 'azioni' => 'Notifica al CSIRT'],
            ['titolo' => 'Attacco ransomware', 'azioni' => 'Ripristino backup'],
            ['titolo' => 'Phishing riuscito', 'azioni' => 'Blocco account compromessi'],
            ['titolo' => 'DDoS', 'azioni' => 'Mitigazione traffico'],
            ['titolo' => 'Accesso non autorizzato', 'azioni' => 'Reset credenziali'],
        ];
        $assets = \App\Models\Asset::all();

        foreach ($assets as $asset) {
            foreach ($scenarios as $sc) {
                \App\Models\Incidente::factory()->create([
                    'asset_id' => $asset->id,
                    'titolo' => $sc['titolo'],
                    'gravita' => fake()->randomElement(['bassa', 'media', 'alta']),
                    'stato' => fake()->randomElement(['aperto', 'in analisi', 'risolto', 'chiuso']),
                    'data_incidente' => fake()->dateTimeBetween('-1 year', 'now'),
                    'azioni_intrapesa' => $sc['azioni'],
                ]);
            }
        }
    }
}
