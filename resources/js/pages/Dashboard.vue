<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import DonutChart from '@/components/charts/DonutChart.vue';

interface UserStats {
    total_donations: number;
    total_donated: number;
    campaigns_created: number;
    active_campaigns: number;
}

interface Campaign {
    id: number;
    title: string;
    description: string;
    current_amount: number;
    target_amount: number;
    creator: {
        name: string;
        department: string;
    };
}

interface Donation {
    id: number;
    amount: number;
    created_at: string;
    campaign: {
        title: string;
    };
}

interface PlatformStats {
    total_campaigns: number;
    active_campaigns: number;
    total_raised: number;
    total_donors: number;
}

interface Props {
    userStats: UserStats;
    featuredCampaigns: Campaign[];
    recentDonations: Donation[];
    platformStats: PlatformStats;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

interface User {
    id: number;
    name: string;
    email: string;
    employee_id: string;
    department: string;
    position: string;
    roles: Array<{ name: string }>;
}

const page = usePage();
const user = computed(() => page.props.auth?.user as User);
const isAdmin = computed(() => 
    user.value?.roles?.some(role => ['admin', 'campaign_manager'].includes(role.name))
);

// Chart for user's donation distribution
const userActivityData = computed(() => ({
    labels: ['Donations Made', 'Campaigns Created'],
    datasets: [{
        data: [props.userStats.total_donations, props.userStats.campaigns_created],
        backgroundColor: ['#3B82F6', '#10B981'],
        borderWidth: 0
    }]
}));

const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
};
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-4 sm:space-y-6">
            <!-- Welcome Section -->
            <div class="bg-white rounded-lg shadow p-4 sm:p-6">
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-2">
                    Welcome back, {{ user?.name }}!
                </h1>
                <p class="text-gray-600 mb-3 sm:mb-4 text-sm sm:text-base">
                    {{ user?.position }} - {{ user?.department }} Department
                </p>
                <p class="text-xs sm:text-sm text-gray-500">
                    Employee ID: {{ user?.employee_id }}
                </p>
            </div>

            <!-- Personal Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 rounded-full">
                            <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">My Donations</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ userStats.total_donations }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 rounded-full">
                            <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Total Donated</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ formatCurrency(userStats.total_donated) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-purple-100 rounded-full">
                            <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">My Campaigns</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ userStats.campaigns_created }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-yellow-100 rounded-full">
                            <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Active Campaigns</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ userStats.active_campaigns }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts and Activity Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- User Activity Chart -->
                <div class="bg-white rounded-lg shadow">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">My Activity</h3>
                        <p class="text-sm text-gray-500">Your contribution breakdown</p>
                    </div>
                    <div class="p-6">
                        <div style="height: 250px;">
                            <DonutChart 
                                v-if="userStats.total_donations > 0 || userStats.campaigns_created > 0" 
                                :data="userActivityData" 
                                :height="250" 
                            />
                            <div v-else class="flex items-center justify-center h-full text-gray-500">
                                <div class="text-center">
                                    <p class="text-lg font-medium">Get Started!</p>
                                    <p class="text-sm">Create a campaign or make a donation</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Donations -->
                <div class="lg:col-span-2 bg-white rounded-lg shadow">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-medium text-gray-900">Recent Donations</h3>
                            <Link href="/donations" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                View All →
                            </Link>
                        </div>
                    </div>
                    <div class="p-6">
                        <div v-if="recentDonations.length > 0" class="space-y-4">
                            <div v-for="donation in recentDonations" :key="donation.id" 
                                 class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900">{{ donation.campaign.title }}</p>
                                    <p class="text-sm text-gray-500">{{ new Date(donation.created_at).toLocaleDateString() }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-green-600">{{ formatCurrency(donation.amount) }}</p>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-8">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                            </svg>
                            <p class="text-lg font-medium text-gray-900 mb-2">No donations yet</p>
                            <p class="text-gray-500 mb-4">Start making a difference by supporting campaigns you care about.</p>
                            <Link href="/campaigns" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                Browse Campaigns
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                <!-- View Campaigns -->
                <Link href="/campaigns" 
                   class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-4 sm:p-6 text-white hover:from-blue-600 hover:to-blue-700 transition-colors">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="min-w-0">
                            <h3 class="font-semibold text-base sm:text-lg truncate">View Campaigns</h3>
                            <p class="text-blue-100 text-xs sm:text-sm">Browse active campaigns</p>
                        </div>
                    </div>
                </Link>

                <!-- Create Campaign -->
                <Link href="/campaigns/create" 
                   class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-4 sm:p-6 text-white hover:from-green-600 hover:to-green-700 transition-colors">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                        </svg>
                        <div class="min-w-0">
                            <h3 class="font-semibold text-base sm:text-lg truncate">Create Campaign</h3>
                            <p class="text-green-100 text-xs sm:text-sm">Start a new fundraiser</p>
                        </div>
                    </div>
                </Link>

                <!-- My Donations -->
                <Link href="/donations" 
                   class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-4 sm:p-6 text-white hover:from-purple-600 hover:to-purple-700 transition-colors">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                        </svg>
                        <div class="min-w-0">
                            <h3 class="font-semibold text-base sm:text-lg truncate">My Donations</h3>
                            <p class="text-purple-100 text-xs sm:text-sm">View donation history</p>
                        </div>
                    </div>
                </Link>

                <!-- Admin Panel -->
                <Link v-if="isAdmin" href="/admin/dashboard" 
                   class="bg-gradient-to-r from-red-500 to-red-600 rounded-lg p-4 sm:p-6 text-white hover:from-red-600 hover:to-red-700 transition-colors">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                        </svg>
                        <div class="min-w-0">
                            <h3 class="font-semibold text-base sm:text-lg truncate">Admin Panel</h3>
                            <p class="text-red-100 text-xs sm:text-sm">Manage platform</p>
                        </div>
                    </div>
                </Link>
            </div>

            <!-- Featured Campaigns Preview -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-4 sm:p-6 border-b border-gray-200">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
                        <h2 class="text-lg sm:text-xl font-semibold text-gray-900">Featured Campaigns</h2>
                        <Link href="/campaigns" class="text-blue-600 hover:text-blue-700 text-sm font-medium self-start sm:self-auto">
                            View All →
                        </Link>
                    </div>
                </div>
                <div class="p-4 sm:p-6">
                    <div v-if="featuredCampaigns.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div v-for="campaign in featuredCampaigns" :key="campaign.id" 
                             class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <h3 class="font-semibold text-gray-900 mb-2">{{ campaign.title }}</h3>
                            <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ campaign.description }}</p>
                            <div class="space-y-2 mb-4">
                                <div class="flex justify-between text-sm">
                                    <span>{{ formatCurrency(campaign.current_amount) }} raised</span>
                                    <span class="text-gray-500">of {{ formatCurrency(campaign.target_amount) }}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" 
                                         :style="{ width: Math.min((campaign.current_amount / campaign.target_amount) * 100, 100) + '%' }"></div>
                                </div>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-500">By {{ campaign.creator.name }}</span>
                                <Link :href="`/campaigns/${campaign.id}`" 
                                      class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                    View →
                                </Link>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center text-gray-500 py-6 sm:py-8">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-lg font-medium">No featured campaigns</p>
                        <p class="text-sm">Check back later for highlighted campaigns</p>
                        <Link href="/campaigns" class="inline-block mt-4 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                            Browse All Campaigns
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Platform Overview -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">Platform Impact</h2>
                    <p class="text-sm text-gray-500">ACME Corp's collective social impact</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-blue-600">{{ platformStats.total_campaigns }}</p>
                            <p class="text-sm text-gray-600">Total Campaigns</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-green-600">{{ platformStats.active_campaigns }}</p>
                            <p class="text-sm text-gray-600">Active Campaigns</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-purple-600">{{ formatCurrency(platformStats.total_raised) }}</p>
                            <p class="text-sm text-gray-600">Total Raised</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-yellow-600">{{ platformStats.total_donors }}</p>
                            <p class="text-sm text-gray-600">Active Donors</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>