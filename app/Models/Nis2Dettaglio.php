<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nis2Dettaglio extends Model
{
    use HasFactory;
    protected $table = 'nis2_dettagli';
    protected $fillable = [
        'id_voce',
        'voce',
        'id_sottovoce',
        'sottovoce',
        'adempimento',
        'documentazione',
    ];
}
