<?php

namespace App\Repositories\Contracts;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface CampaignRepositoryInterface
{
    public function all(): Collection;
    
    public function findById(int $id): ?Campaign;
    
    public function findBySlug(string $slug): ?Campaign;
    
    public function create(array $data): Campaign;
    
    public function update(Campaign $campaign, array $data): bool;
    
    public function delete(Campaign $campaign): bool;
    
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    
    public function getActive(int $perPage = 15): LengthAwarePaginator;
    
    public function getPending(): Collection;
    
    public function getFeatured(): Collection;
    
    public function getByCategory(string $category, int $perPage = 15): LengthAwarePaginator;
    
    public function getByCreator(User $user, int $perPage = 15): LengthAwarePaginator;
    
    public function getTopCampaigns(int $limit = 5): Collection;
    
    public function getCampaignsByCategory(): Collection;
    
    public function search(string $query, int $perPage = 15): LengthAwarePaginator;
    
    public function getExpiringSoon(int $days = 7): Collection;
    
    public function getTotalAmount(): float;
    
    public function getTotalCount(): int;
    
    public function getActiveCampaignCount(): int;
    
    public function getCompletedCampaignCount(): int;
    
    public function getPendingCampaignCount(): int;
}