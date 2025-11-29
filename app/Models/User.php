<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'employee_id',
        'department',
        'position',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function campaigns()
    {
        return $this->hasMany(Campaign::class, 'created_by');
    }

    public function approvedCampaigns()
    {
        return $this->hasMany(Campaign::class, 'approved_by');
    }

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    public function getTotalDonationsAttribute(): float
    {
        return $this->donations()
            ->where('payment_status', Donation::STATUS_COMPLETED)
            ->sum('amount');
    }

    public function canApproveCampaigns(): bool
    {
        return $this->hasRole(['admin', 'campaign_manager']);
    }

    public function canManagePlatform(): bool
    {
        return $this->hasRole('admin');
    }
}
