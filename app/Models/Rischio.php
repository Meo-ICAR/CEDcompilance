<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rischio extends Model
{
    /** @use HasFactory<\Database\Factories\RischioFactory> */
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'titolo',
        'descrizione',
        'probabilita',
        'impatto',
        'stato',
        'azioni_mitigazione',
        'data_valutazione',
    ];

    public function asset()
    {
        return $this->belongsTo(\App\Models\Asset::class);
    }
}
