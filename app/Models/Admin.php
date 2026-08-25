<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Auth\Authenticatable as AuthTrait;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model implements Authenticatable, FilamentUser
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
    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    /** Filament reads $user->name for the sidebar. */
    public function getFilamentName(): string
    {
        return $this->name ?? $this->username ?? 'Admin';
    }
}
