<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';

const props = defineProps({
    donation: {
        type: Object,
        required: true
    }
});

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'My Donations', href: '/donations' },
    { title: `Donation #${props.donation.id}`, href: `/donations/${props.donation.id}` },
];

const getStatusColor = (status) => {
    const colors = {
        'pending': 'bg-yellow-100 text-yellow-800',
        'completed': 'bg-green-100 text-green-800',
        'failed': 'bg-red-100 text-red-800',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};
</script>

<template>
    <Head title="Donation Details" />
    <AppLayout>
        <div class="container mx-auto px-6 py-8">
            <!-- Breadcrumbs -->
            <nav class="flex mb-6" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li v-for="(crumb, index) in breadcrumbs" :key="index" class="inline-flex items-center">
                        <a v-if="index < breadcrumbs.length - 1" :href="crumb.href" 
                           class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                            {{ crumb.title }}
                        </a>
                        <span v-else class="ml-1 text-sm font-medium text-gray-500 md:ml-2">
                            {{ crumb.title }}
                        </span>
                        <svg v-if="index < breadcrumbs.length - 1" class="w-3 h-3 mx-1 text-gray-400" 
                             fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" 
                                  d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" 
                                  clip-rule="evenodd"></path>
                        </svg>
                    </li>
                </ol>
            </nav>

            <div class="max-w-2xl mx-auto">
                <Card>
                    <CardHeader>
                        <div class="flex items-start justify-between">
                            <div>
                                <CardTitle>Donation #{{ donation.id }}</CardTitle>
                                <CardDescription>
                                    Thank you for your contribution!
                                </CardDescription>
                            </div>
                            <Badge :class="getStatusColor(donation.status)">
                                {{ donation.status.charAt(0).toUpperCase() + donation.status.slice(1) }}
                            </Badge>
                        </div>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        <!-- Donation Amount -->
                        <div class="text-center py-6 border-2 border-dashed border-gray-200 rounded-lg">
                            <div class="text-3xl font-bold text-green-600">
                                ${{ Number(donation.amount).toLocaleString() }}
                            </div>
                            <p class="text-gray-600 mt-1">Donation Amount</p>
                        </div>

                        <!-- Donation Details -->
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <h4 class="font-medium text-gray-900">Campaign</h4>
                                    <p class="text-gray-600">{{ donation.campaign.title }}</p>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">Payment Method</h4>
                                    <p class="text-gray-600 capitalize">{{ donation.payment_method.replace('_', ' ') }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <h4 class="font-medium text-gray-900">Date</h4>
                                    <p class="text-gray-600">{{ new Date(donation.created_at).toLocaleDateString() }}</p>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">Anonymous</h4>
                                    <p class="text-gray-600">{{ donation.is_anonymous ? 'Yes' : 'No' }}</p>
                                </div>
                            </div>

                            <div v-if="donation.message">
                                <h4 class="font-medium text-gray-900">Your Message</h4>
                                <p class="text-gray-600 italic bg-gray-50 p-3 rounded-md">
                                    "{{ donation.message }}"
                                </p>
                            </div>

                            <div v-if="donation.transaction_id">
                                <h4 class="font-medium text-gray-900">Transaction ID</h4>
                                <p class="text-gray-600 font-mono text-sm">{{ donation.transaction_id }}</p>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-between pt-6 border-t">
                            <Button variant="outline" @click="$inertia.visit('/donations')">
                                ← Back to My Donations
                            </Button>
                            <Button @click="$inertia.visit(`/campaigns/${donation.campaign.id}`)">
                                View Campaign
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>