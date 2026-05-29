<script setup lang="ts">
import { Plus, Search } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';

defineProps<{
    query: string;
}>();

defineEmits<{
    (e: 'update:query', value: string): void;
    (e: 'add'): void;
}>();
</script>

<template>
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h2 class="text-2xl font-semibold tracking-tight text-slate-950">
                Order
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Daftar seluruh transaksi order.
            </p>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <div class="relative w-full sm:w-[24rem]">
                <Search class="pointer-events-none absolute left-4 top-1/2 size-5 -translate-y-1/2 text-slate-400" />
                <Input
                    :model-value="query"
                    @update:model-value="$emit('update:query', String($event ?? ''))"
                    placeholder="Search order..."
                    class="h-12 rounded-2xl border-slate-200 bg-white pl-11 pr-4 shadow-sm focus-visible:border-red-300 focus-visible:ring-red-200"
                />
            </div>

            <Button
                type="button"
                class="h-12 rounded-2xl bg-gradient-to-r from-red-600 to-red-500 px-5 font-semibold text-white shadow-[0_12px_28px_rgba(239,68,68,0.22)] transition hover:from-red-500 hover:to-red-600"
                @click="$emit('add')"
            >
                <Plus class="size-4" />
                Add Data
            </Button>
        </div>
    </div>
</template>
