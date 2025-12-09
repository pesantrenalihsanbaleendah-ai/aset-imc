<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_code',
        'name',
        'category_id',
        'location_id',
        'responsible_user_id',
        'description',
        'acquisition_price',
        'book_value',
        'condition',
        'status',
        'acquisition_date',
        'warranty_until',
        'serial_number',
        'qr_code',
        'specification',
        'photo_path',
    ];

    protected $casts = [
        'acquisition_date' => 'date',
        'warranty_until' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(AssetCategory::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function responsibleUser()
    {
        return $this->belongsTo(User::class, 'responsible_user_id');
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function maintenances()
    {
        return $this->hasMany(Maintenance::class);
    }

    public function transfers()
    {
        return $this->hasMany(Transfer::class);
    }

    public function disposals()
    {
        return $this->hasMany(Disposal::class);
    }
}
