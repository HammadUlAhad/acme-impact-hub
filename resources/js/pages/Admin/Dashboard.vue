<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';

interface Stats {
    total_campaigns: number;
    active_campaigns: number;
    pending_campaigns: number;
    total_donations: number;
    total_donation_amount: number;
    total_employees: number;
    active_donors: number;
}

interface Campaign {
    id: number;
    title: string;
    status: string;
    current_amount: number;
    target_amount: number;
    created_at: string;
    creator: {
        name: string;
        department: string;
    };
}

interface Donation {
    id: number;
    amount: number;
    created_at: string;
    user: {
        name: string;
        department: string;
    };
    campaign: {
        title: string;
    };
}

interface CategoryData {
    cause_category: string;
    count: number;
    total_raised: number;
}

interface Props {
    stats: Stats;
    recentCampaigns: Campaign[];
    recentDonations: Donation[];
    campaignsByCategory: CategoryData[];
    topCampaigns: Campaign[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Admin', href: '/admin/dashboard' },
];

const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const getStatusColor = (status: string) => {
    const colors = {
        active: 'bg-green-100 text-green-800',
        pending: 'bg-yellow-100 text-yellow-800',
        completed: 'bg-blue-100 text-blue-800',
        cancelled: 'bg-red-100 text-red-800',
    };
    return colors[status as keyof typeof colors] || 'bg-gray-100 text-gray-800';
};

const getCategoryLabel = (category: string) => {
    const labels = {
        education: 'Education',
        health: 'Health & Wellness',
        environment: 'Environment',
        community: 'Community',
        emergency: 'Emergency Relief',
        other: 'Other'
    };
    return labels[category as keyof typeof labels] || category;
};
</script>

<template>
    <Head title="Admin Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6">
            <!-- Header -->
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Admin Dashboard</h1>
                <p class="text-gray-600">Monitor and manage the ACME CSR platform</p>
            </div>

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 rounded-full">
                            <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Total Campaigns</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ stats.total_campaigns }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 rounded-full">
                            <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Active Campaigns</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ stats.active_campaigns }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-purple-100 rounded-full">
                            <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Total Donations</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ stats.total_donations }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-yellow-100 rounded-full">
                            <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Total Raised</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ formatCurrency(stats.total_donation_amount) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Platform Overview</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total Employees</span>
                            <span class="font-medium">{{ stats.total_employees }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Active Donors</span>
                            <span class="font-medium">{{ stats.active_donors }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Participation Rate</span>
                            <span class="font-medium">{{ Math.round((stats.active_donors / stats.total_employees) * 100) }}%</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Pending Approval</span>
                            <span class="font-medium text-yellow-600">{{ stats.pending_campaigns }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Campaigns by Category</h3>
                    <div class="space-y-3">
                        <div v-for="category in campaignsByCategory.slice(0, 5)" :key="category.cause_category" class="flex justify-between items-center">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ getCategoryLabel(category.cause_category) }}</p>
                                <p class="text-xs text-gray-500">{{ category.count }} campaigns</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">{{ formatCurrency(category.total_raised) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                    <div class="space-y-2">
                        <Link href="/campaigns?status=pending" class="block w-full text-left p-2 text-sm text-blue-600 hover:bg-blue-50 rounded">
                            Review Pending Campaigns ({{ stats.pending_campaigns }})
                        </Link>
                        <Link href="/campaigns" class="block w-full text-left p-2 text-sm text-gray-700 hover:bg-gray-50 rounded">
                            Manage All Campaigns
                        </Link>
                        <Link href="/donations" class="block w-full text-left p-2 text-sm text-gray-700 hover:bg-gray-50 rounded">
                            View All Donations
                        </Link>
                        <Link href="/admin/users" class="block w-full text-left p-2 text-sm text-gray-700 hover:bg-gray-50 rounded">
                            Manage Users
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Campaigns -->
                <div class="bg-white rounded-lg shadow">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Recent Campaigns</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div v-for="campaign in recentCampaigns.slice(0, 5)" :key="campaign.id" class="flex items-center justify-between">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">{{ campaign.title }}</p>
                                    <p class="text-xs text-gray-500">
                                        By {{ campaign.creator.name }} • {{ formatDate(campaign.created_at) }}
                                    </p>
                                </div>
                                <div class="text-right ml-4">
                                    <span :class="getStatusColor(campaign.status)" 
                                          class="px-2 py-1 text-xs font-medium rounded-full">
                                        {{ campaign.status }}
                                    </span>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ formatCurrency(campaign.current_amount) }} / {{ formatCurrency(campaign.target_amount) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div v-if="recentCampaigns.length === 0" class="text-center text-gray-500 py-4">
                            No recent campaigns
                        </div>
                    </div>
                </div>

                <!-- Recent Donations -->
                <div class="bg-white rounded-lg shadow">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Recent Donations</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div v-for="donation in recentDonations.slice(0, 5)" :key="donation.id" class="flex items-center justify-between">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">{{ formatCurrency(donation.amount) }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ donation.user.name }} • {{ donation.campaign.title }}
                                    </p>
                                    <p class="text-xs text-gray-400">{{ formatDate(donation.created_at) }}</p>
                                </div>
                            </div>
                        </div>
                        <div v-if="recentDonations.length === 0" class="text-center text-gray-500 py-4">
                            No recent donations
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>