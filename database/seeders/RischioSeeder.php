<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RischioSeeder extends Seeder
{
    public function run(): void
    {
        $threats = [
            ['titolo' => 'Phishing', 'azioni' => 'Formazione personale'],
            ['titolo' => 'Vulnerabilità software', 'azioni' => 'Aggiornamento software'],
            ['titolo' => 'Mancanza MFA', 'azioni' => 'Implementazione MFA'],
            ['titolo' => 'Fornitore terzo', 'azioni' => 'Valutazione rischio supply chain'],
        ];
        $assets = \App\Models\Asset::all();

        foreach ($assets as $asset) {
            foreach ($threats as $th) {
                \App\Models\Rischio::factory()->create([
                    'asset_id' => $asset->id,
                    'titolo' => $th['titolo'],
                    'probabilita' => fake()->randomElement(['bassa', 'media', 'alta']),
                    'impatto' => fake()->randomElement(['basso', 'medio', 'alto']),
                    'stato' => fake()->randomElement(['identificato', 'valutato', 'mitigato', 'accettato']),
                    'azioni_mitigazione' => $th['azioni'],
                    'data_valutazione' => fake()->dateTimeBetween('-1 year', 'now'),
                ]);
            }
        }
    }
}
