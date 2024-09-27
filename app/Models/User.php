<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, HasRoles, HasPanelShield;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'country_id',
        'state_id',
        'city_id',
        'address',
        'postal_code',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
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
        ];
    }

    public function country() {
        return $this->belongsTo(Country::class);
    }

    public function calendars() {
        return $this->belongsToMany(Calendar::class);
    }

    public function departments() {
        return $this->belongsToMany(Department::class);
    }

    public function holidays() {
        return $this->hashMany(Holiday::class);
    }

    public function timesheets() {
        return $this->hashMany(Timesheet::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        $res = false;
        if(
            ($this->hasRole(config('filament-shield.super_admin.name'))) ||
            ($panel->getId() === 'personal' && $this->hasRole(config('filament-shield.panel_user.name')))
            ) {
            $res = true;
        }
        return $res;
    }
}
