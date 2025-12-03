<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

interface Campaign {
    id: number;
    title: string;
    description: string;
    cause_category: string;
    target_amount: number;
    current_amount: number;
    start_date: string;
    end_date: string;
    status: string;
    is_featured: boolean;
    creator: {
        name: string;
        department: string;
    };
    progress_percentage: number;
}

interface Props {
    campaigns: {
        data: Campaign[];
        links: any[];
        meta: any;
    };
    filters: {
        search?: string;
        category?: string;
        status?: string;
    };
    categories: Record<string, string>;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Campaigns', href: '/campaigns' },
];

const searchQuery = ref(props.filters.search || '');
const selectedCategory = ref(props.filters.category || '');
const selectedStatus = ref(props.filters.status || '');

const filteredCampaigns = computed(() => props.campaigns.data || []);

const search = () => {
    router.get('/campaigns', {
        search: searchQuery.value,
        category: selectedCategory.value,
        status: selectedStatus.value,
    }, {
        preserveState: true,
        replace: true,
    });
};

const clearFilters = () => {
    searchQuery.value = '';
    selectedCategory.value = '';
    selectedStatus.value = '';
    router.get('/campaigns');
};

const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
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

const getCategoryColor = (category: string) => {
    const colors = {
        education: 'bg-blue-100 text-blue-800',
        health: 'bg-green-100 text-green-800',
        environment: 'bg-emerald-100 text-emerald-800',
        community: 'bg-purple-100 text-purple-800',
        emergency: 'bg-red-100 text-red-800',
        other: 'bg-gray-100 text-gray-800',
    };
    return colors[category as keyof typeof colors] || 'bg-gray-100 text-gray-800';
};
</script>

<template>
    <Head title="Campaigns" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-4 sm:space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3 sm:gap-4">
                <div class="min-w-0">
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Campaigns</h1>
                    <p class="text-sm sm:text-base text-gray-600 mt-1">Browse and support causes that matter to you</p>
                </div>
                <Link href="/campaigns/create" 
                   class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors text-sm font-medium flex-shrink-0">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                    </svg>
                    <span class="hidden sm:inline">Create Campaign</span>
                    <span class="sm:hidden">Create</span>
                </Link>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-lg shadow p-4 sm:p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="sm:col-span-2 lg:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <input v-model="searchQuery" 
                               @keyup.enter="search"
                               type="text" 
                               placeholder="Search campaigns..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <select v-model="selectedCategory" 
                                @change="search"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                            <option value="">All Categories</option>
                            <option v-for="(label, value) in categories" 
                                    :key="value" 
                                    :value="value">
                                {{ label }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select v-model="selectedStatus" 
                                @change="search"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="pending">Pending</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                    <div class="sm:col-span-2 lg:col-span-1 flex flex-col sm:flex-row sm:items-end gap-2">
                        <button @click="search" 
                                class="w-full sm:w-auto px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors text-sm font-medium">
                            Search
                        </button>
                        <button @click="clearFilters" 
                                class="w-full sm:w-auto px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors text-sm font-medium">
                            Clear
                        </button>
                    </div>
                </div>
            </div>

            <!-- Campaigns Grid -->
            <div v-if="filteredCampaigns.length > 0" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6">
                <div v-for="campaign in filteredCampaigns" 
                     :key="campaign.id" 
                     class="bg-white rounded-lg shadow hover:shadow-md transition-shadow">
                    <!-- Featured Badge -->
                    <div v-if="campaign.is_featured" 
                         class="bg-yellow-500 text-white text-xs font-medium px-3 py-1 rounded-t-lg">
                        ⭐ Featured
                    </div>
                    
                    <div class="p-4 sm:p-6">
                        <!-- Header -->
                        <div class="mb-4">
                            <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-2 line-clamp-2">{{ campaign.title }}</h3>
                            <div class="flex flex-wrap gap-2 mb-2">
                                <span :class="getCategoryColor(campaign.cause_category)" 
                                      class="px-2 py-1 text-xs font-medium rounded-full">
                                    {{ categories[campaign.cause_category] }}
                                </span>
                                <span :class="getStatusColor(campaign.status)" 
                                      class="px-2 py-1 text-xs font-medium rounded-full">
                                    {{ campaign.status }}
                                </span>
                            </div>
                        </div>

                        <!-- Description -->
                        <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                            {{ campaign.description.length > 120 ? campaign.description.substring(0, 120) + '...' : campaign.description }}
                        </p>

                        <!-- Progress -->
                        <div class="mb-4">
                            <div class="flex justify-between text-xs sm:text-sm text-gray-600 mb-1">
                                <span>Progress</span>
                                <span>{{ Math.round(campaign.progress_percentage || 0) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                                     :style="{ width: `${Math.min(campaign.progress_percentage || 0, 100)}%` }">
                                </div>
                            </div>
                            <div class="flex justify-between text-sm text-gray-600 mt-1">
                                <span>{{ formatCurrency(campaign.current_amount) }}</span>
                                <span>{{ formatCurrency(campaign.target_amount) }}</span>
                            </div>
                        </div>

                        <!-- Creator Info -->
                        <div class="text-xs text-gray-500 mb-4">
                            By {{ campaign.creator.name }} • {{ campaign.creator.department }}
                        </div>

                        <!-- Actions -->
                        <div class="flex space-x-2">
                            <Link :href="`/campaigns/${campaign.id}`" 
                               class="flex-1 text-center px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors text-sm">
                                View Details
                            </Link>
                            <Link v-if="campaign.status === 'active'" 
                               :href="`/campaigns/${campaign.id}/donate`" 
                               class="flex-1 text-center px-3 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors text-sm">
                                Donate Now
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="bg-white rounded-lg shadow p-12 text-center">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="text-xl font-medium text-gray-900 mb-2">No campaigns found</h3>
                <p class="text-gray-500 mb-4">Try adjusting your search criteria or create a new campaign.</p>
                <Link href="/campaigns/create" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                    </svg>
                    Create First Campaign
                </Link>
            </div>

            <!-- Pagination -->
            <div v-if="campaigns.links && campaigns.links.length > 3" class="flex justify-center">
                <nav class="inline-flex rounded-md shadow">
                    <Link v-for="link in campaigns.links" 
                       :key="link.label"
                       :href="link.url"
                       :class="[
                           'px-3 py-2 text-sm border',
                           link.active 
                               ? 'bg-blue-600 text-white border-blue-600' 
                               : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                       ]">
                        {{ link.label }}
                    </Link>
                </nav>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>