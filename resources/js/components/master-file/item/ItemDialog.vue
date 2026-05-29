<script setup lang="ts">
import { computed } from 'vue';
import { Form } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import type { ItemDialogMode, ItemRecord } from '@/types/master-file';

const props = defineProps<{
    open: boolean;
    mode: ItemDialogMode;
    record: ItemRecord | null;
    nextCode: string;
}>();

const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
}>();

const isReadOnly = computed(() => props.mode === 'view');
const isCreate = computed(() => props.mode === 'create');
const formKey = computed(
    () =>
        `${props.mode}-${props.record?.id ?? 'new'}-${props.nextCode}-${props.open ? 'open' : 'closed'}`,
);
const title = computed(() => {
    if (props.mode === 'edit') return 'Edit Item';
    if (props.mode === 'view') return 'Detail Item';
    return 'Add Item';
});

const description = computed(() => {
    if (props.mode === 'edit') return 'Perbarui data item yang sudah dipilih.';
    if (props.mode === 'view') return 'Lihat detail item terdaftar.';
    return 'Lengkapi data item baru yang akan disimpan.';
});

const itemCode = computed(() => props.record?.item_code ?? props.nextCode);

const submitAction = computed(() => {
    if (props.mode === 'edit' && props.record) {
        return `/master-file/items/${props.record.id}`;
    }

    return '/master-file/items';
});

const submitMethod = computed(() => (props.mode === 'edit' ? 'put' : 'post'));
</script>

<template>
    <Dialog :open="open" @update:open="emit('update:open', $event)">
        <DialogContent :key="formKey" class="max-h-[90dvh] overflow-y-auto sm:max-w-2xl">
            <Form
                :action="submitAction"
                :method="submitMethod"
                reset-on-success
                :options="{ preserveScroll: true }"
                class="space-y-5"
                v-slot="{ errors, processing, reset, clearErrors }"
                @success="emit('update:open', false)"
            >
                <DialogHeader class="space-y-1.5">
                    <DialogTitle class="text-2xl">{{ title }}</DialogTitle>
                    <DialogDescription>{{ description }}</DialogDescription>
                </DialogHeader>

                <div class="space-y-5">
                    <div class="grid gap-2">
                        <Label for="item_code">Item Number</Label>
                        <Input
                            id="item_code"
                            name="item_code"
                            :model-value="itemCode"
                            disabled
                            class="h-12 rounded-2xl border-slate-200 bg-slate-50 px-4 shadow-sm"
                        />
                        <p class="text-xs text-slate-500">Digenerate otomatis oleh sistem.</p>
                    </div>

                    <div class="grid gap-2">
                        <Label for="description">Deskripsi</Label>
                        <Input
                            id="description"
                            name="description"
                            :disabled="isReadOnly"
                            :default-value="record?.description ?? ''"
                            placeholder="Laptop ASUS Vivobook 14"
                            class="h-12 rounded-2xl border-slate-200 bg-white px-4 shadow-sm focus-visible:border-red-300 focus-visible:ring-red-200"
                        />
                        <InputError :message="errors.description" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="price">Price</Label>
                        <Input
                            id="price"
                            name="price"
                            :disabled="isReadOnly"
                            :default-value="record ? String(record.price) : ''"
                            placeholder="7500000"
                            inputmode="numeric"
                            class="h-12 rounded-2xl border-slate-200 bg-white px-4 shadow-sm focus-visible:border-red-300 focus-visible:ring-red-200"
                        />
                        <InputError :message="errors.price" />
                    </div>
                </div>

                <DialogFooter class="gap-3 pt-2">
                    <Button
                        type="button"
                        variant="outline"
                        class="h-11 rounded-2xl border-slate-200 px-5"
                        @click="
                            () => {
                                clearErrors();
                                reset();
                                emit('update:open', false);
                            }
                        "
                    >
                        {{ isReadOnly ? 'Tutup' : 'Batal' }}
                    </Button>
                    <Button
                        v-if="!isReadOnly"
                        type="submit"
                        class="h-11 rounded-2xl bg-gradient-to-r from-red-600 to-red-500 px-5 font-semibold text-white shadow-[0_12px_28px_rgba(239,68,68,0.22)] transition hover:from-red-500 hover:to-red-600"
                        :disabled="processing"
                    >
                        {{ isCreate ? 'Simpan Data' : 'Update Data' }}
                    </Button>
                </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>
