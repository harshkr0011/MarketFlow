<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rfp extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'title',
        'description',
        'budget_limit',
        'deadline',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function proposals()
    {
        return $this->hasMany(Proposal::class);
    }
}
