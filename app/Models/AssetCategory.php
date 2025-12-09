<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AssetCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'code',
        'depreciation_method',
        'depreciation_years',
    ];

    public function assets()
    {
        return $this->hasMany(Asset::class, 'category_id');
    }
}
