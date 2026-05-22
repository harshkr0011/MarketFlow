<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agency extends Model
{
    protected $fillable = ['name', 'owner_id'];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function workspaces()
    {
        return $this->hasMany(Workspace::class);
    }

    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
