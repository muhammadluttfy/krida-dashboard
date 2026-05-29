<script setup lang="ts">
import { computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { Info, Plus, Trash2 } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { Textarea } from '@/components/ui/textarea';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import OrderCustomerCombobox from '@/components/transaksi/order/OrderCustomerCombobox.vue';
import OrderDatePicker from '@/components/transaksi/order/OrderDatePicker.vue';
import OrderItemCombobox from '@/components/transaksi/order/OrderItemCombobox.vue';
import type {
    OrderDialogMode,
    OrderFormLine,
    OrderFormValues,
    OrderOptionCustomer,
    OrderOptionItem,
    OrderRecord,
} from '@/types/transaksi';

const props = defineProps<{
    open: boolean;
    mode: OrderDialogMode;
    record: OrderRecord | null;
    nextCode: string;
    customers: OrderOptionCustomer[];
    items: OrderOptionItem[];
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
    if (props.mode === 'edit') return 'Edit Order';
    if (props.mode === 'view') return 'Detail Order!';
    return 'Add Order';
});

const description = computed(() => {
    if (props.mode === 'edit') return 'Perbarui transaksi order beserta detail itemnya.';
    if (props.mode === 'view') return 'Lihat detail transaksi order yang tersimpan!';
    return 'Lengkapi header order dan item transaksi baru.';
});

const todayValue = () =>
    new Intl.DateTimeFormat('sv-SE', {
        timeZone: 'Asia/Jakarta',
    }).format(new Date());

const emptyLine = (): OrderFormLine => ({
    item_id: '',
    description: '',
    quantity: '1',
    unit_price: '0',
    discount_percent: '0',
});

const buildInitialState = (): OrderFormValues => ({
    order_number: props.nextCode,
    order_date: todayValue(),
    customer_id: props.customers[0]?.id ? String(props.customers[0].id) : '',
    items: [emptyLine()],
});

const form = useForm<OrderFormValues>(buildInitialState());

const parseNumber = (value: string | number | null | undefined): number => {
    if (typeof value === 'number') {
        return Number.isFinite(value) ? value : 0;
    }

    if (typeof value === 'string') {
        const cleaned = value.replace(/[^\d.-]/g, '');
        return cleaned === '' ? 0 : Number(cleaned);
    }

    return 0;
};

const formatCurrency = (value: string | number) =>
    new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(Number(value ?? 0));

const findItem = (itemId: string) =>
    props.items.find((item) => String(item.id) === itemId) ?? null;

const syncForm = () => {
    const orderItems = props.record?.order_items ?? [];

    const nextState =
        props.mode === 'edit' && props.record
            ? {
                  order_number: props.record.order_number,
                  order_date: props.record.order_date,
                  customer_id: String(props.record.customer_id),
                  items:
                      orderItems.length > 0
                          ? orderItems.map((line) => {
                                const quantity = String(line.quantity ?? 1);
                                const unitPrice = String(line.unit_price ?? 0);
                                const gross = parseNumber(unitPrice) * parseNumber(quantity);
                                const discountAmount = parseNumber(line.discount_amount);
                                const discountPercent =
                                    gross > 0
                                        ? String(
                                              Math.round(
                                                  (discountAmount / gross) * 10000,
                                              ) / 100,
                                          )
                                        : '0';

                                return {
                                    item_id: String(line.item_id),
                                    description: line.description ?? line.item?.description ?? '',
                                    quantity,
                                    unit_price: unitPrice,
                                    discount_percent: discountPercent,
                                };
                            })
                          : [emptyLine()],
              }
            : buildInitialState();

    form.order_number = nextState.order_number;
    form.order_date = nextState.order_date;
    form.customer_id = nextState.customer_id;
    form.items = nextState.items.length > 0 ? nextState.items : [emptyLine()];
    form.clearErrors();
};

watch(
    () => [props.open, props.mode, props.record?.id, props.nextCode],
    () => {
        if (props.open) {
            syncForm();
        }
    },
    { immediate: true },
);

const orderRows = computed(() =>
    form.items.map((row) => {
        const quantity = Math.max(1, Math.floor(parseNumber(row.quantity)));
        const unitPrice = Math.max(0, Math.round(parseNumber(row.unit_price)));
        const discountPercent = Math.max(0, parseNumber(row.discount_percent));
        const gross = quantity * unitPrice;
        const discountAmount = Math.max(0, Math.round(gross * (discountPercent / 100)));
        const lineTotal = Math.max(0, gross - discountAmount);

        return {
            quantity,
            unitPrice,
            discountAmount,
            lineTotal,
        };
    }),
);

const subtotal = computed(() =>
    orderRows.value.reduce((total, row) => total + row.quantity * row.unitPrice, 0),
);
const discountAmount = computed(() =>
    orderRows.value.reduce((total, row) => total + row.discountAmount, 0),
);
const netAmount = computed(() => Math.max(0, subtotal.value - discountAmount.value));
const dppAmount = computed(() => netAmount.value);
const ppnAmount = computed(() => Math.round(dppAmount.value * 0.11));
const grandTotal = computed(() => dppAmount.value + ppnAmount.value);

const addLine = () => {
    if (isReadOnly.value) {
        return;
    }

    form.items.push(emptyLine());
};

const removeLine = (index: number) => {
    if (isReadOnly.value || form.items.length === 1) {
        return;
    }

    form.items.splice(index, 1);
};

const handleItemChange = (index: number, itemId: string) => {
    const line = form.items[index];
    if (!line) {
        return;
    }

    line.item_id = itemId;

    const selected = findItem(itemId);

    if (!selected) {
        line.unit_price = '0';
        return;
    }

    line.unit_price = String(parseNumber(selected.price));
};

const submit = () => {
    if (isReadOnly.value) {
        return;
    }

    const options = {
        preserveScroll: true,
        onSuccess: () => emit('update:open', false),
    };

    if (props.mode === 'edit' && props.record) {
        form.put(`/transaksi/order/${props.record.id}`, options);
        return;
    }

    form.post('/transaksi/order', options);
};

const rowError = (index: number, field: keyof OrderFormLine) =>
    form.errors[`items.${index}.${field}`] ?? '';
</script>

<template>
    <Dialog :open="open" @update:open="emit('update:open', $event)">
        <DialogContent
            :key="formKey"
            class="max-h-[95dvh] overflow-x-hidden overflow-y-auto sm:max-w-[96vw] xl:max-w-7xl"
        >
            <form class="min-w-0 space-y-6" @submit.prevent="submit">
                <DialogHeader class="space-y-1.5">
                    <DialogTitle class="text-2xl">
                        {{ title }}
                    </DialogTitle>
                    <DialogDescription>
                        {{ description }}
                    </DialogDescription>
                </DialogHeader>

                <section class="space-y-4 rounded-[24px] border border-slate-200 bg-slate-50/60 p-5">
                    <div class="grid gap-5 lg:grid-cols-3">
                        <div class="grid gap-2">
                            <Label for="order_number">Nomor Order</Label>
                            <Input
                                id="order_number"
                                name="order_number"
                                :model-value="form.order_number"
                                disabled
                                class="h-12 rounded-2xl border-slate-200 bg-slate-100 px-4 shadow-sm"
                            />
                        </div>

                        <div class="grid gap-2">
                            <Label for="order_date">Tanggal Order</Label>
                            <OrderDatePicker
                                v-model="form.order_date"
                                :disabled="isReadOnly"
                            />
                            <InputError :message="form.errors.order_date" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="customer_id">Nama Customer</Label>
                            <OrderCustomerCombobox
                                v-model="form.customer_id"
                                :customers="customers"
                                :disabled="isReadOnly"
                                placeholder="Pilih customer"
                            />
                            <InputError :message="form.errors.customer_id" />
                        </div>
                    </div>
                </section>

                <section class="min-w-0 space-y-4 rounded-[24px] border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-950">
                                Order Item
                            </h3>
                            <p class="mt-1 text-sm text-slate-500">
                                Tambahkan item transaksi, kuantitas, dan diskon per baris.
                            </p>
                        </div>

                        <Button
                            v-if="!isReadOnly"
                            type="button"
                            class="h-11 rounded-2xl bg-gradient-to-r from-red-600 to-red-500 px-5 font-semibold text-white shadow-[0_12px_28px_rgba(239,68,68,0.22)] transition hover:from-red-500 hover:to-red-600"
                            @click="addLine"
                        >
                            <Plus class="size-4" />
                            Add Item
                        </Button>
                    </div>

                    <div class="min-w-0 overflow-x-auto">
                        <table class="min-w-[1600px] w-full border-collapse">
                            <colgroup>
                                <col class="w-[340px]" />
                                <col class="w-[110px]" />
                                <col class="w-[190px]" />
                                <col class="w-[170px]" />
                                <col class="w-[190px]" />
                                <col class="w-[190px]" />
                                <col class="w-[100px]" />
                            </colgroup>
                            <thead class="bg-slate-50/80 text-left">
                                <tr class="text-sm font-semibold text-slate-700">
                                    <th class="px-4 py-4">Item Number</th>
                                    <th class="px-4 py-4">QTY</th>
                                    <th class="px-4 py-4">Harga</th>
                                    <th class="px-4 py-4">Discount [%]</th>
                                    <th class="px-4 py-4">Cash Disc</th>
                                    <th class="px-4 py-4">Total</th>
                                    <th class="px-4 py-4 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template
                                    v-for="(row, index) in form.items"
                                    :key="`${row.item_id || 'empty'}-${index}`"
                                >
                                    <tr class="border-t border-slate-200/80 align-top text-sm text-slate-600">
                                        <td class="px-4 py-4">
                                            <OrderItemCombobox
                                                :model-value="row.item_id"
                                                :items="items"
                                                :disabled="isReadOnly"
                                                placeholder="Select item"
                                                @update:model-value="(value) => handleItemChange(index, String(value ?? ''))"
                                            />
                                            <InputError :message="rowError(index, 'item_id')" />
                                        </td>
                                        <td class="px-4 py-4">
                                            <Input
                                                v-model="row.quantity"
                                                :disabled="isReadOnly"
                                                inputmode="numeric"
                                                class="h-12 w-full rounded-2xl border-slate-200 bg-white px-3 shadow-sm focus-visible:border-red-300 focus-visible:ring-red-200"
                                            />
                                            <InputError :message="rowError(index, 'quantity')" />
                                        </td>
                                        <td class="px-4 py-4">
                                            <Input
                                                :model-value="formatCurrency(row.unit_price)"
                                                readonly
                                                class="h-12 rounded-2xl border-slate-200 bg-slate-50 px-4 shadow-sm"
                                            />
                                        </td>
                                        <td class="px-4 py-4">
                                            <Input
                                                v-model="row.discount_percent"
                                                :disabled="isReadOnly"
                                                inputmode="decimal"
                                                class="h-12 rounded-2xl border-slate-200 bg-white px-4 shadow-sm focus-visible:border-red-300 focus-visible:ring-red-200"
                                            />
                                            <InputError :message="rowError(index, 'discount_percent')" />
                                        </td>
                                        <td class="px-4 py-4">
                                            <Input
                                                :model-value="formatCurrency(orderRows[index]?.discountAmount ?? 0)"
                                                readonly
                                                class="h-12 rounded-2xl border-slate-200 bg-slate-50 px-4 shadow-sm"
                                            />
                                        </td>
                                        <td class="px-4 py-4">
                                            <Input
                                                :model-value="formatCurrency(orderRows[index]?.lineTotal ?? 0)"
                                                readonly
                                                class="h-12 rounded-2xl border-slate-200 bg-slate-50 px-4 shadow-sm"
                                            />
                                        </td>
                                        <td class="px-4 py-4" rowspan="2">
                                            <div class="flex items-center justify-center pt-2">
                                                <Button
                                                    type="button"
                                                    variant="outline"
                                                    size="icon"
                                                    class="size-11 rounded-2xl border-red-200 bg-white text-red-600 shadow-sm hover:bg-red-50 disabled:cursor-not-allowed"
                                                    :disabled="isReadOnly || form.items.length === 1"
                                                    @click="removeLine(index)"
                                                >
                                                    <Trash2 class="size-4" />
                                                </Button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="border-b border-slate-200/80 text-sm text-slate-600">
                                        <td colspan="6" class="px-4 pb-4 pt-0">
                                            <div class="grid gap-2">
                                                <Label :for="`item-description-${index}`" class="text-sm font-medium text-slate-600">
                                                    Detail Pesanan
                                                </Label>
                                                <Textarea
                                                    :id="`item-description-${index}`"
                                                    v-model="row.description"
                                                    :readonly="isReadOnly"
                                                    placeholder="Tambahkan detail pesanan, permintaan khusus, atau catatan untuk item ini..."
                                                    class="min-h-[96px] rounded-2xl border-slate-200 bg-white px-4 py-3 shadow-sm focus-visible:border-red-300 focus-visible:ring-red-200"
                                                />
                                                <InputError :message="rowError(index, 'description')" />
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="flex min-w-0 justify-end">
                    <aside class="w-full rounded-[24px] border border-slate-200 bg-slate-50/70 p-5 lg:sticky lg:top-5 lg:max-w-[460px]">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="text-lg font-semibold text-slate-950">
                                    Ringkasan
                                </h3>
                                <p class="mt-1 text-sm text-slate-500">
                                    Perhitungan total transaksi order.
                                </p>
                            </div>

                            <Popover>
                                <PopoverTrigger as-child>
                                    <Button
                                        type="button"
                                        variant="outline"
                                        size="icon"
                                        class="size-10 rounded-2xl border-slate-200 bg-white text-slate-600 shadow-sm hover:bg-slate-50"
                                    >
                                        <Info class="size-4" />
                                    </Button>
                                </PopoverTrigger>
                                <PopoverContent side="left" class="w-80">
                                    <div class="flex items-start gap-3">
                                        <div class="flex size-9 items-center justify-center rounded-full bg-red-50 text-red-600">
                                            <Info class="size-4" />
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-slate-950">
                                                Rumus Kalkulasi
                                            </p>
                                            <div class="mt-2 space-y-2 text-sm leading-6 text-slate-600">
                                                <p>Subtotal = jumlah semua qty x harga per item.</p>
                                                <p>Diskon = potongan per baris berdasarkan % diskon.</p>
                                                <p>Netto = Subtotal - Diskon.</p>
                                                <p>DPP = Netto.</p>
                                                <p>PPN = DPP x 11%.</p>
                                                <p>Grand Total = DPP + PPN.</p>
                                            </div>
                                        </div>
                                    </div>
                                </PopoverContent>
                            </Popover>
                        </div>

                        <div class="mt-5 space-y-4">
                            <div class="flex items-center justify-between gap-3 border-b border-slate-200 pb-3">
                                <span class="text-sm font-medium text-slate-600">Subtotal</span>
                                <span class="text-sm font-semibold text-slate-950">
                                    {{ formatCurrency(subtotal) }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between gap-3 border-b border-slate-200 pb-3">
                                <span class="text-sm font-medium text-slate-600">Diskon</span>
                                <span class="text-sm font-semibold text-slate-950">
                                    {{ formatCurrency(discountAmount) }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between gap-3 border-b border-slate-200 pb-3">
                                <span class="text-sm font-medium text-slate-600">Netto</span>
                                <span class="text-sm font-semibold text-slate-950">
                                    {{ formatCurrency(netAmount) }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between gap-3 border-b border-slate-200 pb-3">
                                <span class="text-sm font-medium text-slate-600">DPP</span>
                                <span class="text-sm font-semibold text-slate-950">
                                    {{ formatCurrency(dppAmount) }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between gap-3 border-b border-slate-200 pb-3">
                                <span class="text-sm font-medium text-slate-600">PPN 11%</span>
                                <span class="text-sm font-semibold text-slate-950">
                                    {{ formatCurrency(ppnAmount) }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between gap-3 pt-2">
                                <span class="text-sm font-semibold text-slate-950">Grand Total</span>
                                <span class="text-base font-bold text-red-600">
                                    {{ formatCurrency(grandTotal) }}
                                </span>
                            </div>
                        </div>
                    </aside>
                </section>

                <DialogFooter class="gap-3 pt-2">
                    <Button
                        type="button"
                        variant="outline"
                        class="h-11 rounded-2xl border-slate-200 px-5"
                        :disabled="form.processing"
                        @click="
                            () => {
                                form.clearErrors();
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
                        :disabled="form.processing"
                    >
                        {{ isCreate ? 'Simpan Data' : 'Update Data' }}
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
