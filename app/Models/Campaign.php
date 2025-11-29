<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campaign extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'cause_category',
        'goal_amount',
        'current_amount',
        'start_date',
        'end_date',
        'status',
        'image',
        'created_by',
        'approved_by',
        'approved_at',
        'is_featured',
    ];

    protected $casts = [
        'goal_amount' => 'decimal:2',
        'current_amount' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'approved_at' => 'datetime',
        'is_featured' => 'boolean',
    ];

    // Statuses
    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING = 'pending';
    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    // Cause categories
    const CATEGORY_EDUCATION = 'education';
    const CATEGORY_HEALTH = 'health';
    const CATEGORY_ENVIRONMENT = 'environment';
    const CATEGORY_COMMUNITY = 'community';
    const CATEGORY_EMERGENCY = 'emergency';
    const CATEGORY_OTHER = 'other';

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function getProgressPercentageAttribute(): float
    {
        if ($this->goal_amount <= 0) {
            return 0;
        }
        
        return min(100, ($this->current_amount / $this->goal_amount) * 100);
    }

    public function isAcceptingDonations(): bool
    {
        return $this->status === self::STATUS_ACTIVE
            && $this->end_date > now()
            && $this->current_amount < $this->goal_amount;
    }

    public static function getCauseCategories(): array
    {
        return [
            self::CATEGORY_EDUCATION => 'Education',
            self::CATEGORY_HEALTH => 'Health & Wellness',
            self::CATEGORY_ENVIRONMENT => 'Environment',
            self::CATEGORY_COMMUNITY => 'Community Development',
            self::CATEGORY_EMERGENCY => 'Emergency Relief',
            self::CATEGORY_OTHER => 'Other',
        ];
    }
}
