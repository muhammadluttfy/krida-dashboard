<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { Search } from 'lucide-vue-next';
import { cn } from '@/lib/utils';

const props = defineProps<{
    modelValue?: string;
    placeholder?: string;
    class?: HTMLAttributes['class'];
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void;
}>();
</script>

<template>
    <div class="relative border-b border-slate-200 p-3">
        <Search class="pointer-events-none absolute left-6 top-1/2 size-4 -translate-y-1/2 text-slate-400" />
        <input
            :value="modelValue"
            :placeholder="placeholder ?? 'Search...'"
            class="h-10 w-full rounded-xl border border-slate-200 bg-white pl-10 pr-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-red-300 focus:ring-2 focus:ring-red-200"
            :class="cn(props.class)"
            @input="emit('update:modelValue', String(($event.target as HTMLInputElement).value))"
        >
    </div>
</template>
