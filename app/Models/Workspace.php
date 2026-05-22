<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workspace extends Model
{
    protected $fillable = ['agency_id', 'name'];

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }
}
