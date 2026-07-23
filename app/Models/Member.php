<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'status',
        'subscription_expiry',
    ];

    protected function casts(): array
    {
        return [
            'subscription_expiry' => 'datetime',
        ];
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function isSubscriptionExpired(): bool
    {
        return $this->subscription_expiry && $this->subscription_expiry->isPast();
    }
}
