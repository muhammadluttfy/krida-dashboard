<script setup lang="ts">
import type { PrimitiveProps } from 'reka-ui';
import { PopoverTrigger } from 'reka-ui';

const props = withDefaults(defineProps<PrimitiveProps>(), {
    as: 'button',
});
</script>

<template>
    <PopoverTrigger :as="as" :as-child="asChild">
        <slot />
    </PopoverTrigger>
</template>
