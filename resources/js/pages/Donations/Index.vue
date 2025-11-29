<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';

interface Donation {
    id: number;
    amount: number;
    payment_method: string;
    payment_status: string;
    is_anonymous: boolean;
    donor_message?: string;
    created_at: string;
    processed_at?: string;
    campaign: {
        id: number;
        title: string;
        cause_category: string;
    };
}

interface Stats {
    total_donations: number;
    total_amount: number;
    pending_donations: number;
}

interface Props {
    donations: {
        data: Donation[];
        links: any[];
        meta: any;
    };
    stats: Stats;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'My Donations', href: '/donations' },
];

const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const getStatusColor = (status: string) => {
    const colors = {
        completed: 'bg-green-100 text-green-800',
        pending: 'bg-yellow-100 text-yellow-800',
        processing: 'bg-blue-100 text-blue-800',
        failed: 'bg-red-100 text-red-800',
        refunded: 'bg-gray-100 text-gray-800',
    };
    return colors[status as keyof typeof colors] || 'bg-gray-100 text-gray-800';
};

const getPaymentMethodLabel = (method: string) => {
    const labels = {
        payroll_deduction: 'Payroll Deduction',
        bank_transfer: 'Bank Transfer',
        credit_card: 'Credit Card',
        digital_wallet: 'Digital Wallet',
    };
    return labels[method as keyof typeof labels] || method;
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
    <Head title="My Donations" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6">
            <!-- Header -->
            <div>
                <h1 class="text-2xl font-bold text-gray-900">My Donations</h1>
                <p class="text-gray-600">Track your contributions to ACME Corp CSR initiatives</p>
            </div>

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 rounded-full">
                            <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
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
                        <div class="p-3 bg-green-100 rounded-full">
                            <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Total Amount</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ formatCurrency(stats.total_amount) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-yellow-100 rounded-full">
                            <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Pending</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ stats.pending_donations }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Donations List -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-semibold text-gray-900">Donation History</h2>
                        <Link href="/campaigns" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors text-sm">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                            </svg>
                            Make New Donation
                        </Link>
                    </div>
                </div>

                <div v-if="donations.data.length > 0" class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Campaign
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Amount
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Payment Method
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="donation in donations.data" :key="donation.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ donation.campaign.title }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ getCategoryLabel(donation.campaign.cause_category) }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ formatCurrency(donation.amount) }}
                                    </div>
                                    <div v-if="donation.is_anonymous" class="text-xs text-gray-500">
                                        Anonymous
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ getPaymentMethodLabel(donation.payment_method) }}
                                </td>
                                <td class="px-6 py-4">
                                    <span :class="getStatusColor(donation.payment_status)" 
                                          class="px-2 py-1 text-xs font-medium rounded-full">
                                        {{ donation.payment_status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <div>{{ formatDate(donation.created_at) }}</div>
                                    <div v-if="donation.processed_at" class="text-xs text-green-600">
                                        Processed: {{ formatDate(donation.processed_at) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex space-x-2">
                                        <Link :href="`/donations/${donation.id}`" 
                                           class="text-blue-600 hover:text-blue-700 text-sm">
                                            View Details
                                        </Link>
                                        <Link :href="`/campaigns/${donation.campaign.id}`" 
                                           class="text-green-600 hover:text-green-700 text-sm">
                                            View Campaign
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Empty State -->
                <div v-else class="p-12 text-center">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                    </svg>
                    <h3 class="text-xl font-medium text-gray-900 mb-2">No donations yet</h3>
                    <p class="text-gray-500 mb-4">Start making a difference by supporting campaigns you care about.</p>
                    <Link href="/campaigns" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Browse Campaigns
                    </Link>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="donations.links && donations.links.length > 3" class="flex justify-center">
                <nav class="inline-flex rounded-md shadow">
                    <Link v-for="link in donations.links" 
                       :key="link.label"
                       :href="link.url"
                       v-html="link.label"
                       :class="[
                           'px-3 py-2 text-sm border',
                           link.active 
                               ? 'bg-blue-600 text-white border-blue-600' 
                               : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                       ]">
                    </Link>
                </nav>
            </div>
        </div>
    </AppLayout>
</template>