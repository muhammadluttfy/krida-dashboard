<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AuthLoginLayout from '@/layouts/auth/AuthLoginLayout.vue';

defineOptions({
    layout: AuthLoginLayout,
});
</script>

<template>
    <Head title="Register" />

    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
        Pendaftaran akun sementara dinonaktifkan.
    </div>
</template>
