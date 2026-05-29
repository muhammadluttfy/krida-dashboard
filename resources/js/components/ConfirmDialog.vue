<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';

defineProps<{
    open: boolean;
    title: string;
    description: string;
    confirmLabel?: string;
    cancelLabel?: string;
    processing?: boolean;
}>();

const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
    (e: 'confirm'): void;
}>();
</script>

<template>
    <Dialog :open="open" @update:open="emit('update:open', $event)">
        <DialogContent class="sm:max-w-lg">
            <DialogHeader class="space-y-1.5">
                <DialogTitle class="text-2xl">
                    {{ title }}
                </DialogTitle>
                <DialogDescription class="leading-7">
                    {{ description }}
                </DialogDescription>
            </DialogHeader>

            <DialogFooter class="gap-3 pt-2">
                <Button
                    type="button"
                    variant="outline"
                    class="h-11 rounded-2xl border-slate-200 px-5"
                    :disabled="processing"
                    @click="emit('update:open', false)"
                >
                    {{ cancelLabel ?? 'Cancel' }}
                </Button>
                <Button
                    type="button"
                    variant="destructive"
                    class="h-11 rounded-2xl px-5"
                    :disabled="processing"
                    @click="emit('confirm')"
                >
                    {{ confirmLabel ?? 'Delete' }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
