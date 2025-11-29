<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { Head } from '@inertiajs/vue3';

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Settings', href: '/settings' },
    { title: 'Two-Factor Authentication', href: '/settings/two-factor' },
];
</script>

<template>
    <Head title="Two-Factor Authentication" />
    <AppLayout>
        <SettingsLayout :breadcrumbs="breadcrumbs">
            <div class="text-center py-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-2">Two-Factor Authentication</h2>
                <p class="text-gray-500">Two-factor authentication has been disabled for this platform.</p>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>