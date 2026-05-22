<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'category',
        'price',
        'stock',
        'image_url',
    ];

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
