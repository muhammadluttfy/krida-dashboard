<script setup lang="ts">
import { useDebounceFn } from '@vueuse/core';
import { Check, ChevronsUpDown } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import { Command, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList } from '@/components/ui/command';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { cn } from '@/lib/utils';
import type { OrderOptionItem } from '@/types/transaksi';

const props = defineProps<{
    modelValue: string;
    items: OrderOptionItem[];
    disabled?: boolean;
    placeholder?: string;
    searchEndpoint?: string;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void;
}>();

const open = ref(false);
const search = ref('');
const loading = ref(false);
const options = ref<OrderOptionItem[]>(props.items ?? []);

const selectedItem = computed(() =>
    options.value.find((item) => String(item.id) === String(props.modelValue)) ??
    props.items.find((item) => String(item.id) === String(props.modelValue)) ??
    null,
);

const buttonLabel = computed(() =>
    selectedItem.value
        ? `${selectedItem.value.item_code} - ${selectedItem.value.description}`
        : (props.placeholder ?? 'Select item'),
);

const endpoint = computed(() => props.searchEndpoint ?? '/transaksi/order/items');

const syncInitialOptions = () => {
    options.value = props.items ?? [];
};

watch(
    () => props.items,
    () => {
        if (!search.value.trim()) {
            syncInitialOptions();
        }
    },
    { immediate: true, deep: true },
);

const runSearch = async () => {
    const query = search.value.trim();

    if (query === '') {
        syncInitialOptions();
        loading.value = false;

        return;
    }

    loading.value = true;

    try {
        const url = new URL(endpoint.value, window.location.origin);
        url.searchParams.set('search', query);

        const response = await fetch(url.toString(), {
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        if (!response.ok) {
            throw new Error('Failed to load items.');
        }

        const data = (await response.json()) as OrderOptionItem[];
        options.value = data;
    } catch {
        options.value = props.items ?? [];
    } finally {
        loading.value = false;
    }
};

const debouncedSearch = useDebounceFn(runSearch, 250);

watch(search, () => {
    debouncedSearch();
});

const selectItem = (value: string) => {
    emit('update:modelValue', value === props.modelValue ? '' : value);
    open.value = false;
    search.value = '';
};
</script>

<template>
    <Popover v-model:open="open">
        <PopoverTrigger as-child>
            <Button
                variant="outline"
                role="combobox"
                :aria-expanded="open"
                :disabled="disabled"
                class="h-12 w-full justify-between rounded-2xl border-slate-200 bg-white px-4 text-left text-sm font-normal text-slate-700 shadow-sm hover:bg-slate-50"
            >
                <span class="truncate">{{ buttonLabel }}</span>
                <ChevronsUpDown class="size-4 shrink-0 opacity-50" />
            </Button>
        </PopoverTrigger>

        <PopoverContent class="w-[var(--reka-popper-anchor-width)] p-0">
            <Command>
                <CommandInput
                    v-model="search"
                    placeholder="Search item..."
                />
                <CommandList>
                    <CommandEmpty v-if="options.length === 0">
                        <span v-if="loading">Loading...</span>
                        <span v-else>No item found.</span>
                    </CommandEmpty>
                    <CommandGroup v-else>
                        <CommandItem
                            v-for="item in options"
                            :key="item.id"
                            :value="String(item.id)"
                            @select="selectItem"
                        >
                            <span class="truncate">
                                {{ item.item_code }} - {{ item.description }}
                            </span>
                            <Check
                                :class="
                                    cn(
                                        'ml-auto size-4 shrink-0',
                                        String(props.modelValue) === String(item.id)
                                            ? 'opacity-100'
                                            : 'opacity-0',
                                    )
                                "
                            />
                        </CommandItem>
                    </CommandGroup>
                </CommandList>
            </Command>
        </PopoverContent>
    </Popover>
</template>
