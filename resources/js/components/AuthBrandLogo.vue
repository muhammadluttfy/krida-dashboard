<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { cn } from '@/lib/utils';

defineOptions({
    inheritAttrs: false,
});

defineProps<{
    class?: HTMLAttributes['class'];
}>();
</script>

<template>
    <img
        src="/krida.webp"
        alt="KRIDA"
        :class="
            cn(
                'block object-contain',
                'rounded-none',
                $props.class,
            )
        "
        v-bind="$attrs"
    />
</template>
