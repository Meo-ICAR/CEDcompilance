<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AssetSeeder extends Seeder
{
    public function run(): void
    {
        $categories = ['Server', 'Firewall', 'Database', 'Workstation', 'Cloud Service', 'Network Switch'];
        $orgs = \App\Models\Organizzazione::all();

        foreach ($orgs as $org) {
            foreach ($categories as $cat) {
                \App\Models\Asset::factory()->create([
                    'organizzazione_id' => $org->id,
                    'nome' => $cat . ' ' . fake()->unique()->numerify('##'),
                    'categoria' => $cat,
                    'descrizione' => "Asset di tipo $cat per l'organizzazione {$org->nome}",
                    'ubicazione' => fake()->city(),
                    'responsabile' => fake()->name() . ' (' . fake()->jobTitle() . ')',
                    'stato' => fake()->randomElement(['attivo', 'in manutenzione', 'disattivato']),
                ]);
            }
        }
    }
}
