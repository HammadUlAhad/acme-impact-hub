<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

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
                    <div class="text-center text-gray-500 py-6 sm:py-8">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-lg font-medium">Loading campaigns...</p>
                        <p class="text-sm">Campaign components will be available shortly</p>
                        <Link href="/campaigns" class="inline-block mt-4 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                            Browse All Campaigns
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">Recent Activity</h2>
                </div>
                <div class="p-6">
                    <div class="text-center text-gray-500 py-8">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                        </svg>
                        <p class="text-lg font-medium">Activity feed coming soon</p>
                        <p class="text-sm">Recent donations and campaign updates will appear here</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>