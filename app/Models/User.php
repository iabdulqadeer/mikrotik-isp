<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Cashier\Billable;
use App\Models\Concerns\HasUserSettings;
use Laravel\Sanctum\HasApiTokens;   // ← add this line


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, Billable, HasUserSettings, HasApiTokens;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
      // Either use $fillable ...
    protected $fillable = [
        'first_name','last_name','name','email','phone','whatsapp','customer_care',
        'business_address','country','password','is_active','terms','currency_code','currency_symbol','owner_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
            'trial_ends_at' => 'datetime',
            'trial_started_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'terms' => 'boolean',
            'phone_verified_at'  => 'datetime',
            'phone_verification_expires_at' => 'datetime',
            'two_factor_recovery_codes' => 'array',
            'two_factor_enabled' => 'boolean',
            'two_factor_confirmed_at' => 'datetime',

        ];
    }


    // Optional: convenient accessors
    public function getDisplayCurrencyAttribute(): string
    {
        return $this->currency_code ?: config('currency.default.code');
    }

    public function getDisplaySymbolAttribute(): string
    {
        return $this->currency_symbol ?: config('currency.default.symbol');
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->first_name || $this->last_name) {
            return trim(($this->first_name ?? '').' '.($this->last_name ?? ''));
        }
        return $this->name ?? 'User';
    }


     public function hasVerifiedPhone(): bool
    {
        return ! is_null($this->phone_verified_at);
    }

    public function markPhoneAsVerified(): bool
    {
        return $this->forceFill([
            'phone_verified_at' => now(),
            'phone_verification_code' => null,
            'phone_verification_expires_at' => null,
        ])->save();
    }


    // app/Models/User.php

public function hasActiveSubscription(string $name = 'default'): bool
{
    // Cashier users:
    if (trait_exists(\Laravel\Cashier\Billable::class) && in_array(\Laravel\Cashier\Billable::class, class_uses_recursive(static::class))) {
        return $this->subscribed($name);
    }
    return false;
}

public function isOnTrial(): bool
{
    return $this->trial_ends_at && now()->lt($this->trial_ends_at);
}

public function hasExpiredTrial(): bool
{
    // Consider trial "expired" only if there was a trial end date and we're past it
    return (bool) ($this->trial_ends_at && now()->gte($this->trial_ends_at));
}

public function trialDaysLeft(): int
{
    if (!$this->trial_ends_at) return 0;
    return now()->diffInDays($this->trial_ends_at, false);
}

public function canStartTrial(): bool
{
    if ($this->isOnTrial()) return false;              // already on trial
    if ($this->hasActiveSubscription()) return false;  // no need if subscribed

    // If you added the hardening flags:
    if (isset($this->trial_used) && $this->trial_used) return false;

    // If there is an ended trial timestamp, consider it used
    if ($this->hasExpiredTrial()) return false;

    // No trial started yet → eligible
    return true;
}



    public function logins()
    {
        return $this->hasMany(\App\Models\UserLogin::class)->latest('logged_in_at');
    }


// Optional convenience accessors
    public function unreadCount(): int
    {
        return $this->unreadNotifications()->count();
    }


    public function internetProfile()
    {
        // uses users.internet_profile_id -> internet_profiles.id
        return $this->belongsTo(InternetProfile::class);
    }


    // app/Models/User.php

public function payments()
{
    return $this->hasMany(\App\Models\Payment::class);
}

public function dataUsages()
{
    return $this->hasMany(\App\Models\DataUsage::class);
}


}
