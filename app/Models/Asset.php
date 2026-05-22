<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'agency_id',
        'title',
        'type',
        'thumbnail_path',
        'file_path',
        'category',
        'is_global',
        'price_tier',
        'version_major',
        'version_minor',
        'parent_asset_id',
        'customized_fields_json',
        'expires_at',
        'territory_restriction',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    public function parentAsset()
    {
        return $this->belongsTo(Asset::class, 'parent_asset_id');
    }
}
