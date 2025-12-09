<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'parent_id',
        'level',
        'address',
        'building',
        'floor',
        'room',
    ];

    public function parent()
    {
        return $this->belongsTo(Location::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Location::class, 'parent_id');
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

    public function getFullNameAttribute()
    {
        if ($this->parent) {
            return $this->parent->full_name . ' / ' . $this->name;
        }
        return $this->name;
    }
}
