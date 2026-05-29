<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { useDebounceFn } from '@vueuse/core';
import ConfirmDialog from '@/components/ConfirmDialog.vue';
import {
    OrderDialog,
    OrderTable,
    OrderToolbar,
} from '@/components/transaksi/order';
import type { OrderDialogMode, OrderRecord } from '@/types/transaksi';

type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

type PaginatedOrders = {
    data: OrderRecord[];
    current_page: number;
    from: number | null;
    to: number | null;
    total: number;
    links: PaginationLink[];
};

type Props = {
    orders: PaginatedOrders;
    customers: Array<{
        id: number;
        customer_code: string;
        full_name: string;
    }>;
    items: Array<{
        id: number;
        item_code: string;
        description: string;
        price: string | number;
    }>;
    filters: {
        search: string;
    };
    nextOrderNumber: string;
};

const props = defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Order',
                href: '/transaksi/order',
            },
        ],
        pageSubtitle: 'Kelola data transaksi order',
    },
});

const search = ref(props.filters.search ?? '');
const dialogOpen = ref(false);
const dialogMode = ref<OrderDialogMode>('create');
const activeRecord = ref<OrderRecord | null>(null);
const deleteDialogOpen = ref(false);
const deleteRecord = ref<OrderRecord | null>(null);
const deleteProcessing = ref(false);

const orders = computed(() => props.orders.data ?? []);
const paginationLinks = computed(() => props.orders.links ?? []);

const submitSearch = () => {
    router.get(
        '/transaksi/order',
        {
            search: search.value || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
};

const debouncedSearch = useDebounceFn(submitSearch, 300);

watch(search, () => {
    debouncedSearch();
});

const openCreateDialog = () => {
    dialogMode.value = 'create';
    activeRecord.value = null;
    dialogOpen.value = true;
};

const openViewDialog = (record: OrderRecord) => {
    dialogMode.value = 'view';
    activeRecord.value = record;
    dialogOpen.value = true;
};

const openEditDialog = (record: OrderRecord) => {
    dialogMode.value = 'edit';
    activeRecord.value = record;
    dialogOpen.value = true;
};

const askDelete = (record: OrderRecord) => {
    deleteRecord.value = record;
    deleteDialogOpen.value = true;
};

const confirmDelete = () => {
    if (!deleteRecord.value) {
        return;
    }

    deleteProcessing.value = true;

    router.delete(`/transaksi/order/${deleteRecord.value.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            deleteDialogOpen.value = false;
            deleteRecord.value = null;
        },
        onFinish: () => {
            deleteProcessing.value = false;
        },
    });
};

const showingFrom = computed(() => props.orders.from ?? 0);
const showingTo = computed(() => props.orders.to ?? 0);
const totalItems = computed(() => props.orders.total ?? 0);
</script>

<template>
    <Head title="Order" />

    <div class="flex flex-1 flex-col gap-6 p-4 md:p-6">
        <section class="rounded-[28px] border border-slate-200 bg-white p-6 md:p-8">
            <OrderToolbar v-model:query="search" @add="openCreateDialog" />

            <div class="mt-6">
                <OrderTable
                    :rows="orders"
                    @view="openViewDialog"
                    @edit="openEditDialog"
                    @delete="askDelete"
                />
            </div>

            <div
                class="mt-5 flex flex-col gap-4 border-t border-slate-100 pt-5 text-sm text-slate-500 md:flex-row md:items-center md:justify-between"
            >
                <p>
                    Showing {{ showingFrom }} to {{ showingTo }} of
                    {{ totalItems }} entries
                </p>

                <div class="flex flex-wrap items-center gap-2">
                    <template
                        v-for="(link, index) in paginationLinks"
                        :key="`${link.label}-${index}`"
                    >
                        <button
                            v-if="link.url"
                            type="button"
                            class="inline-flex h-10 min-w-10 items-center justify-center rounded-xl border px-3 text-sm transition"
                            :class="
                                link.active
                                    ? 'border-red-500 bg-red-50 font-semibold text-red-600'
                                    : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50'
                            "
                            v-html="link.label"
                            @click="
                                router.get(link.url, {}, {
                                    preserveState: true,
                                    preserveScroll: true,
                                    replace: true,
                                })
                            "
                        />
                        <span
                            v-else
                            class="inline-flex h-10 min-w-10 items-center justify-center rounded-xl border border-slate-200 bg-slate-50 px-3 text-sm text-slate-400"
                            v-html="link.label"
                        />
                    </template>
                </div>
            </div>
        </section>

        <OrderDialog
            v-model:open="dialogOpen"
            :mode="dialogMode"
            :record="activeRecord"
            :next-code="nextOrderNumber"
            :customers="customers"
            :items="items"
        />

        <ConfirmDialog
            v-model:open="deleteDialogOpen"
            title="Delete order?"
            :description="
                deleteRecord
                    ? `Order ${deleteRecord.order_number} will be removed permanently. This action cannot be undone.`
                    : 'This order will be removed permanently. This action cannot be undone.'
            "
            confirm-label="Delete Data"
            cancel-label="Cancel"
            :processing="deleteProcessing"
            @confirm="confirmDelete"
        />
    </div>
</template>
