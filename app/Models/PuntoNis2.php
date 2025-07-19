<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PuntoNis2 extends Model
{
    protected $table = 'punto_nis2';
    use HasFactory;
    protected $fillable = [
        'id_voce',
        'voce',
        'id_sottovoce',
        'sottovoce',
        'adempimento',
    ];
    public function documenti() {
        return $this->hasMany(DocumentazioneNis2::class, 'punto_nis2_id');
    }
}
