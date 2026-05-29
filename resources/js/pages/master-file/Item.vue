<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { useDebounceFn } from '@vueuse/core';
import ConfirmDialog from '@/components/ConfirmDialog.vue';
import {
    ItemDialog,
    ItemTable,
    ItemToolbar,
} from '@/components/master-file/item';
import type { ItemDialogMode, ItemRecord } from '@/types/master-file';

type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

type PaginatedItems = {
    data: ItemRecord[];
    current_page: number;
    from: number | null;
    to: number | null;
    total: number;
    links: PaginationLink[];
};

type Props = {
    items: PaginatedItems;
    filters: {
        search: string;
    };
    nextItemCode: string;
};

const props = defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Master Items',
                href: '/master-file/items',
            },
        ],
        pageSubtitle: 'Kelola data item',
    },
});

const search = ref(props.filters.search ?? '');
const dialogOpen = ref(false);
const dialogMode = ref<ItemDialogMode>('create');
const activeRecord = ref<ItemRecord | null>(null);
const deleteDialogOpen = ref(false);
const deleteRecord = ref<ItemRecord | null>(null);
const deleteProcessing = ref(false);

const items = computed(() => props.items.data ?? []);
const paginationLinks = computed(() => props.items.links ?? []);

const submitSearch = () => {
    router.get(
        '/master-file/items',
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

const openViewDialog = (record: ItemRecord) => {
    dialogMode.value = 'view';
    activeRecord.value = record;
    dialogOpen.value = true;
};

const openEditDialog = (record: ItemRecord) => {
    dialogMode.value = 'edit';
    activeRecord.value = record;
    dialogOpen.value = true;
};

const askDelete = (record: ItemRecord) => {
    deleteRecord.value = record;
    deleteDialogOpen.value = true;
};

const confirmDelete = () => {
    if (!deleteRecord.value) {
        return;
    }

    deleteProcessing.value = true;

    router.delete(`/master-file/items/${deleteRecord.value.id}`, {
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

const showingFrom = computed(() => props.items.from ?? 0);
const showingTo = computed(() => props.items.to ?? 0);
const totalItems = computed(() => props.items.total ?? 0);
</script>

<template>
    <Head title="Master Items" />

    <div class="flex flex-1 flex-col gap-6 p-4 md:p-6">
        <section class="rounded-[28px] border border-slate-200 bg-white p-6 md:p-8">
            <ItemToolbar v-model:query="search" @add="openCreateDialog" />

            <div class="mt-6">
                <ItemTable
                    :rows="items"
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

    <ItemDialog
        v-model:open="dialogOpen"
        :mode="dialogMode"
        :record="activeRecord"
        :next-code="nextItemCode"
    />

    <ConfirmDialog
        v-model:open="deleteDialogOpen"
        title="Delete item?"
        :description="
            deleteRecord
                ? `Item ${deleteRecord.item_code} - ${deleteRecord.description} will be removed permanently. This action cannot be undone.`
                : 'This item will be removed permanently. This action cannot be undone.'
        "
        confirm-label="Delete Data"
        cancel-label="Cancel"
        :processing="deleteProcessing"
        @confirm="confirmDelete"
    />
</template>
