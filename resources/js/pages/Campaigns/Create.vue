<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

defineProps({
    categories: {
        type: Array,
        default: () => []
    }
});

const form = useForm({
    title: '',
    description: '',
    cause_category: '',
    goal_amount: '',
    start_date: '',
    end_date: '',
    image: null,
});

const imagePreview = ref(null);

const handleImageChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.image = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const submit = () => {
    form.post('/campaigns');
};

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Campaigns', href: '/campaigns' },
    { title: 'Create Campaign', href: '/campaigns/create' },
];
</script>

<template>
    <Head title="Create Campaign" />
    <AppLayout>
        <div class="container mx-auto px-4 sm:px-6 py-4 sm:py-8">
            <!-- Breadcrumbs -->
            <nav class="flex mb-4 sm:mb-6" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li v-for="(crumb, index) in breadcrumbs" :key="index" class="inline-flex items-center">
                        <a v-if="index < breadcrumbs.length - 1" :href="crumb.href" 
                           class="inline-flex items-center text-xs sm:text-sm font-medium text-gray-700 hover:text-blue-600 truncate">
                            {{ crumb.title }}
                        </a>
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

            <div class="max-w-2xl mx-auto">
                <Card>
                    <CardHeader class="px-4 sm:px-6">
                        <CardTitle class="text-lg sm:text-xl">Create New Campaign</CardTitle>
                        <CardDescription class="text-sm sm:text-base">
                            Create a meaningful campaign to drive positive social impact at ACME Corp.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="px-4 sm:px-6">
                        <form @submit.prevent="submit" class="space-y-4 sm:space-y-6">
                            <!-- Campaign Title -->
                            <div class="space-y-2">
                                <Label for="title" class="text-sm sm:text-base font-medium">Campaign Title *</Label>
                                <Input
                                    id="title"
                                    v-model="form.title"
                                    type="text"
                                    placeholder="Enter campaign title..."
                                    :class="{ 'border-red-500': form.errors.title }"
                                    class="h-12 text-base"
                                    required
                                />
                                <p v-if="form.errors.title" class="text-sm text-red-600">
                                    {{ form.errors.title }}
                                </p>
                            </div>

                            <!-- Description -->
                            <div class="space-y-2">
                                <Label for="description" class="text-sm sm:text-base font-medium">Description *</Label>
                                <textarea
                                    id="description"
                                    v-model="form.description"
                                    placeholder="Describe your campaign's purpose and impact..."
                                    rows="4"
                                    required
                                    :class="[
                                        'w-full px-3 py-3 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-vertical text-base min-h-[120px]',
                                        form.errors.description ? 'border-red-500' : 'border-gray-300'
                                    ]"
                                ></textarea>
                                <p v-if="form.errors.description" class="text-sm text-red-600">
                                    {{ form.errors.description }}
                                </p>
                            </div>

                            <!-- Cause Category -->
                            <div class="space-y-2">
                                <Label for="cause_category" class="text-sm sm:text-base font-medium">Cause Category *</Label>
                                <select
                                    id="cause_category"
                                    v-model="form.cause_category"
                                    required
                                    :class="[
                                        'w-full px-3 py-3 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-base h-12',
                                        form.errors.cause_category ? 'border-red-500' : 'border-gray-300'
                                    ]"
                                >
                                    <option value="" disabled>Select a cause category</option>
                                    <option v-for="(label, key) in categories" :key="key" :value="key">
                                        {{ label }}
                                    </option>
                                </select>
                                <p v-if="form.errors.cause_category" class="text-sm text-red-600">
                                    {{ form.errors.cause_category }}
                                </p>
                            </div>

                            <!-- Goal Amount -->
                            <div class="space-y-2">
                                <Label for="goal_amount" class="text-sm sm:text-base font-medium">Fundraising Goal *</Label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-base">$</span>
                                    <Input
                                        id="goal_amount"
                                        v-model="form.goal_amount"
                                        type="number"
                                        step="0.01"
                                        min="1"
                                        placeholder="0.00"
                                        class="pl-8 h-12 text-base"
                                        :class="{ 'border-red-500': form.errors.goal_amount }"
                                        required
                                    />
                                </div>
                                <p v-if="form.errors.goal_amount" class="text-sm text-red-600">
                                    {{ form.errors.goal_amount }}
                                </p>
                            </div>

                            <!-- Date Fields Row -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <!-- Start Date -->
                                <div class="space-y-2">
                                    <Label for="start_date" class="text-sm sm:text-base font-medium">Start Date *</Label>
                                    <Input
                                        id="start_date"
                                        v-model="form.start_date"
                                        type="date"
                                        class="h-12 text-base"
                                        :class="{ 'border-red-500': form.errors.start_date }"
                                        required
                                    />
                                    <p v-if="form.errors.start_date" class="text-sm text-red-600">
                                        {{ form.errors.start_date }}
                                    </p>
                                </div>

                                <!-- End Date -->
                                <div class="space-y-2">
                                    <Label for="end_date" class="text-sm sm:text-base font-medium">End Date *</Label>
                                    <Input
                                        id="end_date"
                                        v-model="form.end_date"
                                        type="date"
                                        class="h-12 text-base"
                                        :class="{ 'border-red-500': form.errors.end_date }"
                                        required
                                    />
                                    <p v-if="form.errors.end_date" class="text-sm text-red-600">
                                        {{ form.errors.end_date }}
                                    </p>
                                </div>
                            </div>

                            <!-- Campaign Image -->
                            <div class="space-y-2">
                                <Label for="image" class="text-sm sm:text-base font-medium">Campaign Image</Label>
                                <Input
                                    id="image"
                                    type="file"
                                    accept="image/*"
                                    @change="handleImageChange"
                                    class="h-12 text-base file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                    :class="{ 'border-red-500': form.errors.image }"
                                />
                                <p v-if="form.errors.image" class="text-sm text-red-600">
                                    {{ form.errors.image }}
                                </p>
                                <!-- Image Preview -->
                                <div v-if="imagePreview" class="mt-4">
                                    <img :src="imagePreview" alt="Campaign preview" class="w-full max-w-md h-48 object-cover rounded-md">
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between pt-6 gap-3">
                                <Button 
                                    type="button" 
                                    variant="outline" 
                                    @click="$inertia.visit('/campaigns')"
                                    class="h-12 text-base order-2 sm:order-1"
                                >
                                    Cancel
                                </Button>
                                <Button 
                                    type="submit" 
                                    :disabled="form.processing"
                                    class="h-12 text-base order-1 sm:order-2"
                                >
                                    <span v-if="form.processing">Creating...</span>
                                    <span v-else>Create Campaign</span>
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>