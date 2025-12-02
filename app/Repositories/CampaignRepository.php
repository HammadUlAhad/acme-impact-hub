<?php

namespace App\Repositories;

use App\Models\Campaign;
use App\Models\User;
use App\Repositories\Contracts\CampaignRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class CampaignRepository implements CampaignRepositoryInterface
{
    public function all(): Collection
    {
        return Campaign::with(['creator', 'approver'])->get();
    }
    
    public function findById(int $id): ?Campaign
    {
        return Campaign::with(['creator', 'approver', 'donations'])->find($id);
    }
    
    public function findBySlug(string $slug): ?Campaign
    {
        return Campaign::with(['creator', 'approver', 'donations'])
            ->where('slug', $slug)
            ->first();
    }
    
    public function create(array $data): Campaign
    {
        return Campaign::create($data);
    }
    
    public function update(Campaign $campaign, array $data): bool
    {
        return $campaign->update($data);
    }
    
    public function delete(Campaign $campaign): bool
    {
        return $campaign->delete();
    }
    
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Campaign::with(['creator', 'approver'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
    
    public function getActive(int $perPage = 15): LengthAwarePaginator
    {
        return Campaign::with(['creator'])
            ->where('status', Campaign::STATUS_ACTIVE)
            ->where('end_date', '>=', now())
            ->orderBy('featured', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
    
    public function getPending(): Collection
    {
        return Campaign::with(['creator'])
            ->where('status', Campaign::STATUS_PENDING)
            ->orderBy('created_at', 'asc')
            ->get();
    }
    
    public function getFeatured(): Collection
    {
        return Campaign::with(['creator'])
            ->where('status', Campaign::STATUS_ACTIVE)
            ->where('featured', true)
            ->where('end_date', '>=', now())
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();
    }
    
    public function getByCategory(string $category, int $perPage = 15): LengthAwarePaginator
    {
        return Campaign::with(['creator'])
            ->where('status', Campaign::STATUS_ACTIVE)
            ->where('cause_category', $category)
            ->where('end_date', '>=', now())
            ->orderBy('featured', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
    
    public function getByCreator(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return Campaign::with(['approver'])
            ->where('creator_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
    
    public function getTopCampaigns(int $limit = 5): Collection
    {
        return Campaign::with(['creator'])
            ->where('status', Campaign::STATUS_ACTIVE)
            ->orderBy('current_amount', 'desc')
            ->limit($limit)
            ->get();
    }
    
    public function getCampaignsByCategory(): Collection
    {
        return Campaign::selectRaw('cause_category, COUNT(*) as count, SUM(current_amount) as total_raised')
            ->where('status', Campaign::STATUS_ACTIVE)
            ->groupBy('cause_category')
            ->get();
    }
    
    public function search(string $query, int $perPage = 15): LengthAwarePaginator
    {
        return Campaign::with(['creator'])
            ->where('status', Campaign::STATUS_ACTIVE)
            ->where(function ($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                    ->orWhere('description', 'LIKE', "%{$query}%")
                    ->orWhere('cause_category', 'LIKE', "%{$query}%");
            })
            ->orderBy('featured', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
    
    public function getExpiringSoon(int $days = 7): Collection
    {
        return Campaign::with(['creator'])
            ->where('status', Campaign::STATUS_ACTIVE)
            ->where('end_date', '<=', now()->addDays($days))
            ->where('end_date', '>=', now())
            ->orderBy('end_date', 'asc')
            ->get();
    }
    
    public function getTotalAmount(): float
    {
        return (float) Campaign::where('status', Campaign::STATUS_ACTIVE)->sum('current_amount');
    }
    
    public function getTotalCount(): int
    {
        return Campaign::count();
    }
    
    public function getActiveCampaignCount(): int
    {
        return Campaign::where('status', Campaign::STATUS_ACTIVE)->count();
    }
    
    public function getCompletedCampaignCount(): int
    {
        return Campaign::where('status', Campaign::STATUS_COMPLETED)->count();
    }
    
    public function getPendingCampaignCount(): int
    {
        return Campaign::where('status', Campaign::STATUS_PENDING)->count();
    }
}