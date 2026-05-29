<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { login } from '@/routes';
</script>

<template>
    <Head title="Welcome" />
    <div class="flex min-h-screen items-center justify-center p-6">
        <Link
            :href="login()"
            class="inline-flex h-12 items-center justify-center rounded-xl bg-red-600 px-5 text-sm font-semibold text-white"
        >
            Go to Login
        </Link>
    </div>
</template>
