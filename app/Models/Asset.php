<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    /** @use HasFactory<\Database\Factories\AssetFactory> */
    use HasFactory;

    protected $fillable = [
        'organizzazione_id',
        'nome',
        'categoria',
        'descrizione',
        'ubicazione',
        'responsabile',
        'stato',
    ];

    public function organizzazione()
    {
        return $this->belongsTo(\App\Models\Organizzazione::class);
    }
}
