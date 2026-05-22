<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
// Uncomment these once you install filament and scout via composer
// use Filament\Models\Contracts\FilamentUser;
// use Filament\Panel;
// use Laravel\Scout\Searchable;

class User extends Authenticatable // implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, Billable, \Spatie\Permission\Traits\HasRoles; // , Searchable

    // public function canAccessPanel(Panel $panel): bool
    // {
    //     return $this->is_admin;
    // }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'agency_id',
        'client_id',
        'ltv',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    public function isAdmin()
    {
        return $this->is_admin;
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

    public function campaigns()
    {
        return $this->hasMany(Campaign::class);
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];


}
