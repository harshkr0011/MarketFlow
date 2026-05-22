<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'scope',
        'owner_id',
        'total_amount',
        'currency',
        'fiscal_year',
    ];

    public function drawdowns()
    {
        return $this->hasMany(BudgetDrawdown::class);
    }
}
