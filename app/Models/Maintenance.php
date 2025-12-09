<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Maintenance extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'requested_by',
        'approved_by',
        'scheduled_date',
        'maintenance_date',
        'type',
        'description',
        'findings',
        'actions_taken',
        'cost',
        'status',
        'document_path',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'maintenance_date' => 'date',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
