<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'incidente_id',
        'sent_to',
        'sent_at',
        'type',
        'status',
        'message',
    ];

    public function incidente()
    {
        return $this->belongsTo(\App\Models\Incidente::class);
    }
}
