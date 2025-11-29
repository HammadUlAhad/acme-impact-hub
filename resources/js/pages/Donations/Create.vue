<script setup>
import { ref, computed } from 'vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const props = defineProps({
    campaign: {
        type: Object,
        required: true
    }
});

const form = useForm({
    campaign_id: props.campaign.id,
    amount: '',
    payment_method: 'credit_card',
    is_anonymous: false,
    message: '',
});

const submit = () => {
    form.post(route('donations.store'));
};

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Campaigns', href: '/campaigns' },
    { title: props.campaign.title, href: `/campaigns/${props.campaign.id}` },
    { title: 'Donate', href: `/donations/create?campaign_id=${props.campaign.id}` },
];

const progressPercentage = computed(() => {
    if (!props.campaign.goal_amount) return 0;
    return Math.min((props.campaign.total_raised / props.campaign.goal_amount) * 100, 100);
});

const predefinedAmounts = [25, 50, 100, 250, 500, 1000];
const paymentMethods = [
    { value: 'credit_card', label: 'Credit Card' },
    { value: 'bank_transfer', label: 'Bank Transfer' },
    { value: 'payroll_deduction', label: 'Payroll Deduction' },
];
</script>

<template>
    <Head title="Make a Donation" />
    <AppLayout>
        <div class="container mx-auto px-4 sm:px-6 py-4 sm:py-8">
            <!-- Breadcrumbs -->
            <nav class="flex mb-4 sm:mb-6" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li v-for="(crumb, index) in breadcrumbs" :key="index" class="inline-flex items-center">
                        <Link v-if="index < breadcrumbs.length - 1" :href="crumb.href" 
                           class="inline-flex items-center text-xs sm:text-sm font-medium text-gray-700 hover:text-blue-600 truncate">
                            {{ crumb.title }}
                        </Link>
                        <span v-else class="ml-1 text-xs sm:text-sm font-medium text-gray-500 md:ml-2 truncate">
                            {{ crumb.title }}
                        </span>
                        <svg v-if="index < breadcrumbs.length - 1" class="w-3 h-3 mx-1 text-gray-400 flex-shrink-0" 
                             fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" 
                                  d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" 
                                  clip-rule="evenodd"></path>
                        </svg>
                    </li>
                </ol>
            </nav>

            <div class="max-w-4xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8">
                <!-- Campaign Info -->
                <div class="order-2 lg:order-1">
                    <Card>
                        <CardHeader class="px-4 sm:px-6">
                            <CardTitle class="text-lg sm:text-xl">{{ campaign.title }}</CardTitle>
                            <CardDescription class="text-sm sm:text-base">{{ campaign.cause_category }}</CardDescription>
                        </CardHeader>
                        <CardContent class="px-4 sm:px-6">
                            <!-- Progress -->
                            <div class="space-y-2 mb-4">
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
                            
                            <p class="text-gray-700 text-sm line-clamp-4">{{ campaign.description }}</p>
                            
                            <div class="mt-4 text-sm text-gray-500">
                                Created by {{ campaign.creator.name }}
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Donation Form -->
                <div class="order-1 lg:order-2">
                    <Card>
                        <CardHeader class="px-4 sm:px-6">
                            <CardTitle class="text-lg sm:text-xl">Make a Donation</CardTitle>
                            <CardDescription class="text-sm sm:text-base">
                                Your contribution makes a difference in creating positive social impact.
                            </CardDescription>
                        </CardHeader>
                        <CardContent class="px-4 sm:px-6">
                            <form @submit.prevent="submit" class="space-y-4 sm:space-y-6">
                                <!-- Amount Selection -->
                                <div class="space-y-3">
                                    <Label class="text-sm sm:text-base font-medium">Donation Amount *</Label>
                                    
                                    <!-- Predefined Amounts -->
                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 mb-3">
                                        <button
                                            v-for="amount in predefinedAmounts"
                                            :key="amount"
                                            type="button"
                                            @click="form.amount = amount"
                                            :class="[
                                                'px-4 py-3 border rounded-md text-sm sm:text-base font-medium transition-colors min-h-[48px] touch-manipulation',
                                                form.amount == amount 
                                                    ? 'border-blue-600 bg-blue-50 text-blue-700' 
                                                    : 'border-gray-300 hover:border-gray-400'
                                            ]"
                                        >
                                            ${{ amount }}
                                        </button>
                                    </div>

                                    <!-- Custom Amount -->
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-base">$</span>
                                        <Input
                                            v-model="form.amount"
                                            type="number"
                                            step="0.01"
                                            min="1"
                                            placeholder="Enter custom amount"
                                            class="pl-8 h-12 text-base"
                                            :class="{ 'border-red-500': form.errors.amount }"
                                            required
                                        />
                                    </div>
                                    <p v-if="form.errors.amount" class="text-sm text-red-600">
                                        {{ form.errors.amount }}
                                    </p>
                                </div>

                                <!-- Payment Method -->
                                <div class="space-y-2">
                                    <Label for="payment_method" class="text-sm sm:text-base font-medium">Payment Method *</Label>
                                    <select
                                        id="payment_method"
                                        v-model="form.payment_method"
                                        required
                                        :class="[
                                            'w-full px-3 py-3 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-base h-12',
                                            form.errors.payment_method ? 'border-red-500' : 'border-gray-300'
                                        ]"
                                    >
                                        <option value="" disabled>Select payment method</option>
                                        <option v-for="method in paymentMethods" :key="method.value" :value="method.value">
                                            {{ method.label }}
                                        </option>
                                    </select>
                                    <p v-if="form.errors.payment_method" class="text-sm text-red-600">
                                        {{ form.errors.payment_method }}
                                    </p>
                                </div>

                                <!-- Anonymous Donation -->
                                <div class="flex items-center space-x-3 py-2">
                                    <input
                                        id="is_anonymous"
                                        v-model="form.is_anonymous"
                                        type="checkbox"
                                        class="h-5 w-5 rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                                    />
                                    <Label for="is_anonymous" class="text-sm sm:text-base cursor-pointer">
                                        Make this donation anonymous
                                    </Label>
                                </div>

                                <!-- Optional Message -->
                                <div class="space-y-2">
                                    <Label for="message" class="text-sm sm:text-base font-medium">Optional Message</Label>
                                    <textarea
                                        id="message"
                                        v-model="form.message"
                                        placeholder="Add an inspiring message (optional)..."
                                        rows="3"
                                        :class="[
                                            'w-full px-3 py-3 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-vertical text-base min-h-[96px]',
                                            form.errors.message ? 'border-red-500' : 'border-gray-300'
                                        ]"
                                    ></textarea>
                                    <p v-if="form.errors.message" class="text-sm text-red-600">
                                        {{ form.errors.message }}
                                    </p>
                                </div>

                                <!-- Submit Button -->
                                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between pt-6 gap-3">
                                    <Button 
                                        type="button" 
                                        variant="outline" 
                                        @click="$inertia.visit(`/campaigns/${campaign.id}`)"
                                        class="h-12 text-base order-2 sm:order-1"
                                    >
                                        Cancel
                                    </Button>
                                    <Button 
                                        type="submit" 
                                        :disabled="form.processing" 
                                        size="lg"
                                        class="h-12 text-base order-1 sm:order-2"
                                    >
                                        <span v-if="form.processing">Processing...</span>
                                        <span v-else>Donate ${{ form.amount || 0 }}</span>
                                    </Button>
                                </div>
                            </form>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>