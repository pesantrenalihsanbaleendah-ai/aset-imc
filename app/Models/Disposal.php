<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Disposal extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'requested_by',
        'approved_by',
        'reason',
        'description',
        'estimated_residual_value',
        'actual_residual_value',
        'disposal_date',
        'status',
        'notes',
        'document_path',
    ];

    protected $casts = [
        'disposal_date' => 'date',
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
