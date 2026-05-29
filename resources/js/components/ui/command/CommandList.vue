<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { cn } from '@/lib/utils';

const props = defineProps<{
    class?: HTMLAttributes['class'];
}>();
</script>

<template>
    <div data-slot="command-list" :class="cn('max-h-72 overflow-y-auto p-1', props.class)">
        <slot />
    </div>
</template>
