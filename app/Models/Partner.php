<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'email',
        'status',
    ];

    public function proposals()
    {
        return $this->hasMany(Proposal::class);
    }
}
