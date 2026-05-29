<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { cn } from '@/lib/utils';

const props = defineProps<{
    value: string;
    class?: HTMLAttributes['class'];
}>();

const emit = defineEmits<{
    (e: 'select', value: string): void;
}>();
</script>

<template>
    <button
        type="button"
        data-slot="command-item"
        :class="
            cn(
                'flex w-full items-center rounded-xl px-3 py-2 text-left text-sm text-slate-700 outline-none transition hover:bg-slate-50 focus:bg-red-50 focus:text-red-700',
                props.class,
            )
        "
        @click="emit('select', props.value)"
    >
        <slot />
    </button>
</template>
