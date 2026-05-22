<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdSpendLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'platform',
        'spend_amount',
        'currency',
        'recorded_date',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}
