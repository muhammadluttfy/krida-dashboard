<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Order',
                href: '/transaksi/order',
            },
        ],
        pageSubtitle: 'Transaksi',
    },
});
</script>

<template>
    <Head title="Order" />

    <div class="flex flex-1 items-center justify-center p-6">
        <div class="w-full max-w-3xl rounded-[28px] border border-dashed border-slate-200 bg-white p-8 text-center shadow-sm">
            <h2 class="text-2xl font-semibold text-slate-950">Order</h2>
            <p class="mt-3 text-slate-500">
                Halaman ini nanti dipakai untuk transaksi sales order.
            </p>
        </div>
    </div>
</template>
