<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentazioneNis2 extends Model
{
    protected $table = 'documentazione_nis2';
    use HasFactory;
    protected $fillable = [
        'punto_nis2_id',
        'documento',
    ];
    public function punto() {
        return $this->belongsTo(PuntoNis2::class, 'punto_nis2_id');
    }
}
