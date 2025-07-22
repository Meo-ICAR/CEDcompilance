<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Policy extends Model
{
    protected $fillable = [
        'title',
        'version',
        'description',
        'file_path',
        'effective_date',
        'reviewed_at',
    ];
}
