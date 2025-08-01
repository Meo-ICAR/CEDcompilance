<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingEvent extends Model
{
    protected $fillable = [
        'user_id',
        'topic',
        'date',
        'completed',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
