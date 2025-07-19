<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PuntoNis2;
use App\Models\DocumentazioneNis2;

class Nis2Seeder extends Seeder
{
    public function run(): void
    {
        $punti = [
            [
                'id_voce' => '1',
                'voce' => 'Governance e Responsabilità',
                'id_sottovoce' => '1.1',
                'sottovoce' => 'Impegno e Supervisione del CdA',
                'adempimento' => "Gli organi di gestione (CdA, Amministratore Unico) devono approvare le policy di cybersecurity, supervisionarne l'attuazione e sono ritenuti direttamente responsabili in caso di non conformità.",
                'documenti' => [
                    'Verbali del CdA/organo di gestione che approvano e riesaminano la strategia e le policy di sicurezza',
                    'Matrice di assegnazione responsabilità (RACI Matrix)',
                ],
            ],
            [
                'id_voce' => '1',
                'voce' => 'Governance e Responsabilità',
                'id_sottovoce' => '1.2',
                'sottovoce' => 'Formazione del Management',
                'adempimento' => 'Gli organi di gestione devono seguire una formazione specifica e regolare per comprendere e valutare i rischi e le pratiche di cybersecurity.',
                'documenti' => [
                    'Attestati di partecipazione alla formazione',
                    'Programma del corso di formazione seguito dal management',
                ],
            ],
            [
                'id_voce' => '2',
                'voce' => 'Misure di Gestione del Rischio (CRM) - Art. 21',
                'id_sottovoce' => '2.1',
                'sottovoce' => 'Policy di Analisi del Rischio e Sicurezza dei Sistemi',
                'adempimento' => "Adottare e mantenere aggiornate policy per l'analisi dei rischi e per la sicurezza dei sistemi informativi, volte a proteggere le reti e le informazioni.",
                'documenti' => [
                    'Policy di Sicurezza delle Informazioni (PSI)',
                    'Policy di Analisi del Rischio',
                    'Documento di Valutazione dei Rischi (Risk Assessment)',
                ],
            ],
            [
                'id_voce' => '2',
                'voce' => 'Misure di Gestione del Rischio (CRM) - Art. 21',
                'id_sottovoce' => '2.2',
                'sottovoce' => 'Gestione degli Incidenti',
                'adempimento' => 'Stabilire e attuare procedure per prevenire, rilevare, analizzare, gestire e rispondere agli incidenti di sicurezza per minimizzarne l\'impatto.',
                'documenti' => [
                    'Piano di Gestione degli Incidenti (Incident Response Plan)',
                    'Procedure operative di classificazione e risposta',
                    'Registri e report degli incidenti',
                ],
            ],
            // ... (continua per tutti i punti del CSV fornito)
        ];

        foreach ($punti as $punto) {
            $p = PuntoNis2::create([
                'id_voce' => $punto['id_voce'],
                'voce' => $punto['voce'],
                'id_sottovoce' => $punto['id_sottovoce'],
                'sottovoce' => $punto['sottovoce'],
                'adempimento' => $punto['adempimento'],
            ]);
            foreach ($punto['documenti'] as $doc) {
                DocumentazioneNis2::create([
                    'punto_nis2_id' => $p->id,
                    'documento' => trim($doc),
                ]);
            }
        }
    }
}
