<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { useDebounceFn } from '@vueuse/core';
import ConfirmDialog from '@/components/ConfirmDialog.vue';
import {
    CustomerDialog,
    CustomerTable,
    CustomerToolbar,
} from '@/components/master-file/customer';
import type { CustomerDialogMode, CustomerRecord } from '@/types/master-file';

type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

type PaginatedCustomers = {
    data: CustomerRecord[];
    current_page: number;
    from: number | null;
    to: number | null;
    total: number;
    links: PaginationLink[];
};

type Props = {
    customers: PaginatedCustomers;
    filters: {
        search: string;
    };
    nextCustomerCode: string;
};

const props = defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Master Customer',
                href: '/master-file/customers',
            },
        ],
        pageSubtitle: '',
    },
});

const search = ref(props.filters.search ?? '');
const dialogOpen = ref(false);
const dialogMode = ref<CustomerDialogMode>('create');
const activeRecord = ref<CustomerRecord | null>(null);
const deleteDialogOpen = ref(false);
const deleteRecord = ref<CustomerRecord | null>(null);
const deleteProcessing = ref(false);

const customers = computed(() => props.customers.data ?? []);
const paginationLinks = computed(() => props.customers.links ?? []);

const submitSearch = () => {
    router.get(
        '/master-file/customers',
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

const openViewDialog = (record: CustomerRecord) => {
    dialogMode.value = 'view';
    activeRecord.value = record;
    dialogOpen.value = true;
};

const openEditDialog = (record: CustomerRecord) => {
    dialogMode.value = 'edit';
    activeRecord.value = record;
    dialogOpen.value = true;
};

const askDelete = (record: CustomerRecord) => {
    deleteRecord.value = record;
    deleteDialogOpen.value = true;
};

const confirmDelete = () => {
    if (!deleteRecord.value) {
        return;
    }

    deleteProcessing.value = true;

    router.delete(`/master-file/customers/${deleteRecord.value.id}`, {
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

const showingFrom = computed(() => props.customers.from ?? 0);
const showingTo = computed(() => props.customers.to ?? 0);
const totalItems = computed(() => props.customers.total ?? 0);
</script>

<template>
    <Head title="Master Customer" />

    <div class="flex flex-1 flex-col gap-6 p-4 md:p-6">
        <section class="rounded-[28px] border border-slate-200 bg-white p-6 md:p-8">
            <CustomerToolbar
                v-model:query="search"
                @add="openCreateDialog"
            />

            <div class="mt-6">
                <CustomerTable
                    :rows="customers"
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
    </div>

    <CustomerDialog
        v-model:open="dialogOpen"
        :mode="dialogMode"
        :record="activeRecord"
        :next-code="nextCustomerCode"
    />

    <ConfirmDialog
        v-model:open="deleteDialogOpen"
        title="Delete customer?"
        :description="
            deleteRecord
                ? `Customer ${deleteRecord.customer_code} - ${deleteRecord.full_name} will be removed permanently. This action cannot be undone.`
                : 'This customer will be removed permanently. This action cannot be undone.'
        "
        confirm-label="Delete Data"
        cancel-label="Cancel"
        :processing="deleteProcessing"
        @confirm="confirmDelete"
    />
</template>
