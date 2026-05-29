<script setup lang="ts">
import { computed } from 'vue';
import { Form } from '@inertiajs/vue3';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import type { CustomerDialogMode, CustomerRecord } from '@/types/master-file';

const props = defineProps<{
    open: boolean;
    mode: CustomerDialogMode;
    record: CustomerRecord | null;
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
    if (props.mode === 'edit') return 'Edit Customer';
    if (props.mode === 'view') return 'Detail Customer';
    return 'Add Customer';
});

const description = computed(() => {
    if (props.mode === 'edit') return 'Perbarui data customer yang sudah dipilih.';
    if (props.mode === 'view') return 'Lihat detail customer terdaftar.';
    return 'Lengkapi data customer baru yang akan disimpan.';
});

const customerCode = computed(
    () => (props.record?.customer_code ?? props.nextCode),
);

const submitAction = computed(() => {
    if (props.mode === 'edit' && props.record) {
        return `/master-file/customers/${props.record.id}`;
    }

    return '/master-file/customers';
});

const submitMethod = computed(() => (props.mode === 'edit' ? 'put' : 'post'));
</script>

<template>
    <Dialog :open="open" @update:open="emit('update:open', $event)">
        <DialogContent
            :key="formKey"
            class="max-h-[90dvh] overflow-y-auto sm:max-w-2xl"
        >
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
                        <Label for="customer_code">Kode Customer</Label>
                        <Input
                            id="customer_code"
                            name="customer_code"
                            :model-value="customerCode"
                            disabled
                            class="h-12 rounded-2xl border-slate-200 bg-slate-50 px-4 shadow-sm"
                        />
                        <p class="text-xs text-slate-500">
                            Digenerate otomatis oleh sistem.
                        </p>
                    </div>

                    <div class="grid gap-2">
                        <Label for="full_name">Nama Customer</Label>
                        <Input
                            id="full_name"
                            name="full_name"
                            :disabled="isReadOnly"
                            :default-value="record?.full_name ?? ''"
                            placeholder="PT. Sejahtera Abadi"
                            class="h-12 rounded-2xl border-slate-200 bg-white px-4 shadow-sm focus-visible:border-red-300 focus-visible:ring-red-200"
                        />
                        <InputError :message="errors.full_name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="address">Alamat</Label>
                        <textarea
                            id="address"
                            name="address"
                            :disabled="isReadOnly"
                            rows="4"
                            placeholder="Jl. Merdeka No. 10, Jakarta Pusat"
                            class="min-h-28 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm outline-none transition focus:border-red-300 focus:ring-2 focus:ring-red-200 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-500"
                        >{{ record?.address ?? '' }}</textarea>
                        <InputError :message="errors.address" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="phone_number">Nomor Telepon</Label>
                        <Input
                            id="phone_number"
                            name="phone_number"
                            :disabled="isReadOnly"
                            :default-value="record?.phone_number ?? ''"
                            placeholder="081234567890"
                            inputmode="numeric"
                            class="h-12 rounded-2xl border-slate-200 bg-white px-4 shadow-sm focus-visible:border-red-300 focus-visible:ring-red-200"
                        />
                        <InputError :message="errors.phone_number" />
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
