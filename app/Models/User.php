<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Eloquent replacement for the legacy `ad_user` table.
 * The table uses a `password_hash` column instead of Laravel's default
 * `password`, so we map it through `getAuthPassword()`.
 */
class User extends Model implements Authenticatable
{
    use AuthTrait, Notifiable, HasApiTokens;

    protected $table = 'user';

    /**
     * Explicit whitelist — never mass-assign privilege columns
     * (user_type, status, group_id, password_hash, forgot, confirm).
     * Those are only written via forceFill() at trusted, validated call
     * sites (AuthController, UserAdminController).
     */
    protected $fillable = [
        'username', 'email', 'name', 'phone', 'city', 'country', 'address',
        'tagline', 'description', 'website', 'image', 'sex', 'postcode',
        'facebook', 'twitter', 'googleplus', 'instagram', 'linkedin', 'youtube',
        'oauth_provider', 'oauth_link', 'created_at', 'updated_at',
    ];

    public $timestamps = true;

    protected $hidden = [
        'password_hash', 'forgot', 'forgot_expires_at', 'confirm', 'oauth_uid',
    ];

    public function getAuthPassword(): string
    {
        return (string) $this->password_hash;
    }

    // Relationships
    public function posts()          { return $this->hasMany(Post::class, 'user_id'); }
    public function favourites()     { return $this->hasMany(Favourite::class, 'user_id'); }
    public function transactions()   { return $this->hasMany(Transaction::class, 'seller_id'); }
    public function messagesSent()   { return $this->hasMany(Message::class, 'from_id'); }
    public function messagesRecv()   { return $this->hasMany(Message::class, 'to_id'); }
    public function upgrades()       { return $this->hasMany(Upgrade::class, 'user_id'); }
    public function mobileNumber()   { return $this->hasOne(MobileNumber::class, 'user_id'); }
    public function options()        { return $this->hasMany(UserOption::class, 'user_id'); }

    /**
     * Admin check. In the legacy Bylancer schema, admins live in a separate
     * `admins` table keyed by email / username. We also accept a direct
     * user_type=admin flag when the schema constraint is relaxed.
     */
    public function isAdmin(): bool
    {
        if ((string) ($this->user_type ?? '') === 'admin') return true;
        return Admin::query()
            ->where(function ($q) {
                $q->where('email',    $this->email)
                  ->orWhere('username', $this->username);
            })
            ->exists();
    }

    /**
     * A user becomes a "shop" the moment they have at least one Post
     * (published, pending, sold or otherwise). This means every seller
     * automatically gets access to the /shop dashboard.
     */
    public function isShop(): bool
    {
        return Post::where('user_id', $this->id)->exists();
    }
}
