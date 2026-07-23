<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Fortify\Fortify;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'locale',
        'must_change_password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * Get the attributes that should be cast.
    //  *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'must_change_password' => 'boolean',
        ];
    }

    /**
     * Send the password reset notification (custom design).
     */
    public function sendPasswordResetNotification(#[\SensitiveParameter] $token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function getRawTwoFactorSecretAttribute(): string
    {
        return Fortify::currentEncrypter()->decrypt($this->attributes['two_factor_secret']);
    }

    /**
     * Whether the current session has a valid recent password confirmation.
     * Set by Laravel Fortify when the user confirms their password via the confirm-password flow.
     * Used to authorize sensitive actions (e.g. delete account, 2FA) without re-asking for the password.
     *
     * @see \Laravel\Fortify\Http\Controllers\ConfirmablePasswordController
     */
    public function hasRecentlyConfirmedPassword(): bool
    {
        $confirmedAt = session('auth.password_confirmed_at');

        if (!$confirmedAt) {
            return false;
        }

        return (time() - $confirmedAt) <= config('auth.password_timeout', 10800);
    }
}
