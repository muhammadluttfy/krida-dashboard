<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { cn } from '@/lib/utils';

const props = defineProps<{
    class?: HTMLAttributes['class'];
}>();
</script>

<template>
    <div
        data-slot="command"
        :class="cn('flex w-full flex-col overflow-hidden rounded-2xl bg-white text-slate-950', props.class)"
    >
        <slot />
    </div>
</template>
