<script setup lang="ts">
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';

const props = defineProps({
    campaign: {
        type: Object,
        required: true
    },
    donations: {
        type: Object,
        default: () => ({})
    },
    canEdit: {
        type: Boolean,
        default: false
    },
    canDelete: {
        type: Boolean,
        default: false
    }
});

const progressPercentage = computed(() => {
    if (!props.campaign.goal_amount) return 0;
    return Math.min((props.campaign.total_raised / props.campaign.goal_amount) * 100, 100);
});

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Campaigns', href: '/campaigns' },
    { title: props.campaign.title, href: `/campaigns/${props.campaign.id}` },
];
</script>

<template>
    <Head :title="campaign.title" />
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

            <div class="max-w-4xl mx-auto">
                <!-- Campaign Header -->
                <Card class="mb-6">
                    <CardHeader>
                        <div class="flex items-start justify-between">
                            <div>
                                <CardTitle class="text-2xl mb-2">{{ campaign.title }}</CardTitle>
                                <div class="flex items-center space-x-4 mb-4">
                                    <Badge variant="outline">{{ campaign.cause_category }}</Badge>
                                    <Badge :variant="campaign.status === 'active' ? 'default' : 'secondary'">
                                        {{ campaign.status }}
                                    </Badge>
                                    <Badge v-if="campaign.is_featured" variant="destructive">Featured</Badge>
                                </div>
                            </div>
                            <div v-if="canEdit || canDelete" class="flex space-x-2">
                                <Button v-if="canEdit" :href="`/campaigns/${campaign.id}/edit`" variant="outline">
                                    Edit
                                </Button>
                                <Button v-if="canDelete" variant="destructive">
                                    Delete
                                </Button>
                            </div>
                        </div>
                        
                        <!-- Progress -->
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="font-medium">${{ Number(campaign.total_raised || 0).toLocaleString() }} raised</span>
                                <span class="text-gray-500">of ${{ Number(campaign.goal_amount).toLocaleString() }} goal</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" :style="{ width: progressPercentage + '%' }"></div>
                            </div>
                            <div class="text-right text-sm text-gray-500">
                                {{ Math.round(progressPercentage) }}% complete
                            </div>
                        </div>
                    </CardHeader>
                    
                    <CardContent>
                        <p class="text-gray-700 whitespace-pre-line">{{ campaign.description }}</p>
                        
                        <div class="mt-6 flex items-center justify-between">
                            <div class="text-sm text-gray-500">
                                Created by {{ campaign.creator.name }} • 
                                {{ new Date(campaign.start_date).toLocaleDateString() }} - 
                                {{ new Date(campaign.end_date).toLocaleDateString() }}
                            </div>
                            
                            <Button :href="`/donations/create?campaign_id=${campaign.id}`" size="lg">
                                Donate Now
                            </Button>
                        </div>
                    </CardContent>
                </Card>

                <!-- Recent Donations -->
                <Card v-if="donations.data?.length">
                    <CardHeader>
                        <CardTitle>Recent Donations</CardTitle>
                        <CardDescription>
                            Latest contributions to this campaign
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div v-for="donation in donations.data" :key="donation.id" 
                                 class="flex items-center justify-between p-4 border rounded-lg">
                                <div>
                                    <p class="font-medium">{{ donation.donor.name }}</p>
                                    <p class="text-sm text-gray-500">{{ new Date(donation.created_at).toLocaleDateString() }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-green-600">${{ Number(donation.amount).toLocaleString() }}</p>
                                    <p class="text-sm text-gray-500">{{ donation.payment_method }}</p>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>