<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'user_id',
        'requester_name',
        'responsible_person',
        'approver_id',
        'purpose',
        'loan_date',
        'expected_return_date',
        'actual_return_date',
        'status',
        'notes',
        'document_path',
    ];

    protected $casts = [
        'loan_date' => 'datetime',
        'expected_return_date' => 'datetime',
        'actual_return_date' => 'datetime',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function isOverdue()
    {
        return $this->status === 'pending' && now()->isAfter($this->expected_return_date);
    }
}
