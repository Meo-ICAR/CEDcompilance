<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organizzazione extends Model
{
    /** @use HasFactory<\Database\Factories\OrganizzazioneFactory> */
    use HasFactory;

    protected $fillable = [
        'nome',
        'partita_iva',
        'indirizzo',
        'citta',
        'provincia',
        'cap',
        'paese',
        'referente',
        'email_referente',
        'telefono_referente',
    ];
}
