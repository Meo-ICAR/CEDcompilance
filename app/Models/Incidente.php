<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incidente extends Model
{
    /** @use HasFactory<\Database\Factories\IncidenteFactory> */
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'titolo',
        'descrizione',
        'gravita',
        'stato',
        'data_incidente',
        'azioni_intrapesa',
    ];

    public function asset()
    {
        return $this->belongsTo(\App\Models\Asset::class);
    }
}
