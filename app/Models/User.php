<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'theme', 'plan', 'subscription_status', 'trial_ends_at', 'premium_until'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

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
            'trial_ends_at' => 'datetime',
            'premium_until' => 'datetime',
        ];
    }

    public function categorias(): HasMany
    {
        return $this->hasMany(Categoria::class);
    }

    public function lancamentos(): HasMany
    {
        return $this->hasMany(Lancamento::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function isAdmin(): bool
    {
        return $this->plan === 'admin';
    }

    public function hasActiveTrial(): bool
    {
        return $this->subscription_status === 'trial'
            && $this->trial_ends_at
            && $this->trial_ends_at->isFuture();
    }

    public function hasActivePremium(): bool
    {
        return $this->subscription_status === 'active'
            && $this->premium_until
            && $this->premium_until->isFuture();
    }

    public function hasPremiumAccess(): bool
    {
        return $this->isAdmin() || $this->hasActiveTrial() || $this->hasActivePremium();
    }

    public function normalizeSubscriptionState(): void
    {
        if ($this->isAdmin()) {
            return;
        }

        if (
            $this->subscription_status === 'trial'
            && $this->trial_ends_at
            && $this->trial_ends_at->isPast()
            && blank($this->premium_until)
        ) {
            $this->forceFill([
                'plan' => 'starter',
                'subscription_status' => 'expired',
                'theme' => 'systex-default',
            ])->save();
        }
    }

    protected function theme(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value ?: 'systex-default',
        );
    }
}
