<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplianceTask extends Model
{
    protected $fillable = [
        'title',
        'description',
        'due_date',
        'completed_at',
        'assigned_to',
        'status',
        'related_model_type',
        'related_model_id',
    ];

    public function assignedUser()
    {
        return $this->belongsTo(\App\Models\User::class, 'assigned_to');
    }
}
