<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Backup extends Model
{
    protected $fillable = [
        'asset_id',
        'backup_date',
        'status',
        'tested_at',
        'notes',
    ];

    public function asset()
    {
        return $this->belongsTo(\App\Models\Asset::class);
    }
}
