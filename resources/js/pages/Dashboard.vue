<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowRight, Folder, NotebookPen, Sparkles } from 'lucide-vue-next';
import { computed } from 'vue';
import { dashboard } from '@/routes';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
        ],
        pageSubtitle: 'Sales Order Management System',
    },
});

const masterFileUrl = '/master-file/customers';
const orderUrl = '/transaksi/order';
const cards = computed(() => [
    {
        title: 'Master File',
        description: 'Kelola data master customer dan items dengan rapi dan cepat.',
        href: masterFileUrl,
        icon: Folder,
    },
    {
        title: 'Transaksi',
        description: 'Kelola transaksi sales order dari awal hingga selesai.',
        href: orderUrl,
        icon: NotebookPen,
    },
]);
</script>

<template>
    <Head title="Dashboard" />

    <div class="flex min-h-[calc(100dvh-8rem)] flex-1 flex-col gap-6 overflow-x-hidden p-4 md:p-6">
        <section
            class="relative overflow-hidden rounded-[28px] border border-red-100 bg-[linear-gradient(135deg,rgba(255,255,255,1)_0%,rgba(255,248,248,1)_48%,rgba(255,238,238,1)_100%)] p-6 shadow-[0_20px_60px_rgba(15,23,42,0.06)] md:p-8"
        >
            <div
                class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top_right,_rgba(239,68,68,0.08),_transparent_25%),radial-gradient(circle_at_bottom_left,_rgba(239,68,68,0.05),_transparent_20%)]"
            />
            <div class="relative grid gap-8 lg:grid-cols-[1.2fr_.8fr] lg:items-center">
                <div class="space-y-5">
                    <div class="inline-flex items-center gap-2 rounded-full bg-red-50 px-4 py-2 text-sm font-medium text-red-600">
                        <Sparkles class="size-4" />
                        Selamat datang kembali
                    </div>
                    <div>
                        <h2 class="text-3xl font-semibold tracking-tight text-slate-950 md:text-5xl">
                            Admin User
                            <span class="inline-block align-middle">👋</span>
                        </h2>
                        <p class="mt-4 max-w-2xl text-base leading-8 text-slate-600 md:text-lg">
                            Pilih menu di bawah ini untuk mengelola data master
                            dan transaksi penjualan dengan mudah.
                        </p>
                    </div>
                </div>

                <div class="hidden lg:block" />
            </div>
        </section>

        <section class="grid gap-5 md:grid-cols-2">
            <article
                v-for="card in cards"
                :key="card.title"
                class="group relative overflow-hidden rounded-[28px] border border-slate-200 bg-white p-6 shadow-[0_18px_50px_rgba(15,23,42,0.06)] transition hover:-translate-y-1 hover:shadow-[0_22px_60px_rgba(15,23,42,0.1)] md:p-7"
            >
                <div class="absolute right-10 top-10 grid grid-cols-5 gap-1 opacity-30">
                    <span
                        v-for="i in 20"
                        :key="`${card.title}-dot-${i}`"
                        class="size-1 rounded-full bg-red-300"
                    />
                </div>
                <div class="relative flex h-full flex-col">
                    <div class="mx-auto flex size-20 items-center justify-center rounded-full bg-red-50 text-red-500 shadow-[0_10px_30px_rgba(239,68,68,0.08)]">
                        <component :is="card.icon" class="size-9" />
                    </div>
                    <h3 class="mt-8 text-center text-2xl font-semibold tracking-tight text-slate-950">
                        {{ card.title }}
                    </h3>
                    <div class="mx-auto mt-4 h-1 w-16 rounded-full bg-red-500" />
                    <p class="mx-auto mt-6 max-w-md text-center text-base leading-7 text-slate-600">
                        {{ card.description }}
                    </p>
                    <div class="mt-auto pt-8">
                        <Link
                            :href="card.href"
                            class="inline-flex h-12 items-center justify-center gap-2 rounded-2xl border border-red-300 px-6 text-sm font-medium text-red-600 transition hover:bg-red-50"
                        >
                            Buka Menu
                            <ArrowRight class="size-4 transition group-hover:translate-x-0.5" />
                        </Link>
                    </div>
                </div>
            </article>
        </section>

    </div>
</template>
