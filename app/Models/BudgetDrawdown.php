<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetDrawdown extends Model
{
    use HasFactory;

    protected $fillable = [
        'budget_id',
        'campaign_id',
        'amount_requested',
        'status',
    ];

    public function budget()
    {
        return $this->belongsTo(Budget::class);
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}
