<?php

namespace App\Repositories\Contracts;

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface DonationRepositoryInterface
{
    public function findById(int $id): ?Donation;
    
    public function create(array $data): Donation;
    
    public function update(Donation $donation, array $data): bool;
    
    public function delete(Donation $donation): bool;
    
    public function getByUser(User $user, int $perPage = 15): LengthAwarePaginator;
    
    public function getByCampaign(Campaign $campaign, int $perPage = 15): LengthAwarePaginator;
    
    public function getRecent(int $limit = 10): Collection;
    
    public function getPending(): Collection;
    
    public function getProcessing(): Collection;
    
    public function getCompleted(): Collection;
    
    public function getTotalAmount(): float;
    
    public function getTotalCount(): int;
    
    public function getCompletedCount(): int;
    
    public function getPendingCount(): int;
    
    public function getUserTotalDonations(User $user): float;
    
    public function getUserDonationCount(User $user): int;
    
    public function getUserCompletedDonationCount(User $user): int;
    
    public function getUserPendingDonationCount(User $user): int;
    
    public function getUserDonationsToCampaign(User $user, Campaign $campaign): float;
    
    public function getDonationTrends(int $days = 30): Collection;
    
    public function getPaymentMethodBreakdown(int $days = 30): Collection;
    
    public function getDonationsByDateRange(\DateTime $startDate, \DateTime $endDate): Collection;
}