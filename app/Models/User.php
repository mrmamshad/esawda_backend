<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable as AuthTrait;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
    use AuthTrait, HasApiTokens, HasFactory, Notifiable;

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
        'oauth_provider', 'oauth_link', 'shop_name', 'shop_category', 'shop_description',
        'shop_address', 'shop_banner', 'shop_documents', 'created_at', 'updated_at',
    ];

    public $timestamps = true;

    protected $casts = [
        'plan_expires_at' => 'datetime',
        'shop_documents' => 'array',
        'shop_verified_at' => 'datetime',
        'lastactive' => 'datetime',
    ];

    protected $hidden = [
        'password_hash', 'forgot', 'forgot_expires_at', 'confirm', 'oauth_uid',
    ];

    protected $appends = [
        'is_admin', 'is_shop',
    ];

    public function getAuthPassword(): string
    {
        return (string) $this->password_hash;
    }

    // Relationships
    public function posts()
    {
        return $this->hasMany(Post::class, 'user_id');
    }

    public function favourites()
    {
        return $this->hasMany(Favourite::class, 'user_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'seller_id');
    }

    public function messagesSent()
    {
        return $this->hasMany(Message::class, 'from_id');
    }

    public function messagesRecv()
    {
        return $this->hasMany(Message::class, 'to_id');
    }

    public function upgrades()
    {
        return $this->hasMany(Upgrade::class, 'user_id');
    }

    public function mobileNumber()
    {
        return $this->hasOne(MobileNumber::class, 'user_id');
    }

    public function options()
    {
        return $this->hasMany(UserOption::class, 'user_id');
    }

    /**
     * Admin check. In the legacy Bylancer schema, admins live in a separate
     * `admins` table keyed by email / username. We also accept a direct
     * user_type=admin flag when the schema constraint is relaxed.
     */
    public function isAdmin(): bool
    {
        if ((string) ($this->user_type ?? '') === 'admin') {
            return true;
        }

        return Admin::query()
            ->where(function ($q) {
                $q->where('email', $this->email)
                    ->orWhere('username', $this->username);
            })
            ->exists();
    }

    public function getIsAdminAttribute(): bool
    {
        return $this->isAdmin();
    }

    /**
     * A user becomes a "shop" (corporate panel) only when they have opened
     * a shop via onboarding (user_type = seller). Regular buyers who merely
     * purchase products stay on the lightweight /dashboard — they never
     * gain shop-panel access just by owning a post or making a purchase.
     */
    public function isShop(): bool
    {
        return (string) ($this->user_type ?? '') === 'seller';
    }

    public function getIsShopAttribute(): bool
    {
        return $this->isShop();
    }
}
