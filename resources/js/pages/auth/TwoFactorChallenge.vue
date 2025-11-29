<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { onMounted } from 'vue';

// Since 2FA is disabled, redirect to dashboard
onMounted(() => {
    router.visit('/dashboard');
});
</script>

<template>
    <div class="flex items-center justify-center min-h-screen">
        <div class="text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
            <p class="mt-4 text-gray-600">Redirecting to dashboard...</p>
        </div>
    </div>
</template>
