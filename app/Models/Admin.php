<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthTrait;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model implements Authenticatable, \Filament\Models\Contracts\FilamentUser
{
    use AuthTrait;

    protected $table = 'admins';
    public $timestamps = false;
    protected $guarded = [];
    protected $hidden = ['password_hash'];

    public function getAuthPassword(): string
    {
        return (string) $this->password_hash;
    }

    /** Filament allows every admin row into the panel. */
    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        return true;
    }

    /** Filament reads $user->name for the sidebar. */
    public function getFilamentName(): string
    {
        return $this->name ?? $this->username ?? 'Admin';
    }
}
