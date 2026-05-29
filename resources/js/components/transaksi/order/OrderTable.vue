<script setup lang="ts">
import { Eye, Pencil, Trash2 } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import type { OrderRecord } from '@/types/transaksi';

defineProps<{
    rows: OrderRecord[];
}>();

defineEmits<{
    (e: 'view', row: OrderRecord): void;
    (e: 'edit', row: OrderRecord): void;
    (e: 'delete', row: OrderRecord): void;
}>();

const formatDate = (value: string) =>
    new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    }).format(new Date(`${value}T00:00:00`));

const formatCurrency = (value: string | number) =>
    new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(Number(value ?? 0));
</script>

<template>
    <div class="overflow-hidden rounded-[24px] border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-[980px] w-full border-collapse">
                <thead class="bg-slate-50/80 text-left">
                    <tr class="text-sm font-semibold text-slate-700">
                        <th class="px-5 py-4">Nomor Order</th>
                        <th class="px-5 py-4">Tanggal Order</th>
                        <th class="px-5 py-4">Nama Customer</th>
                        <th class="px-5 py-4">Total</th>
                        <th class="px-5 py-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="row in rows"
                        :key="row.id"
                        class="border-t border-slate-200/80 text-sm text-slate-600 transition hover:bg-slate-50/70"
                    >
                        <td class="px-5 py-5 font-medium text-slate-700">
                            {{ row.order_number }}
                        </td>
                        <td class="px-5 py-5">
                            {{ formatDate(row.order_date) }}
                        </td>
                        <td class="px-5 py-5">
                            {{ row.customer?.full_name ?? '-' }}
                        </td>
                        <td class="px-5 py-5">
                            {{ formatCurrency(row.grand_total) }}
                        </td>
                        <td class="px-5 py-5">
                            <div class="flex items-center justify-center gap-3">
                                <Button
                                    type="button"
                                    variant="outline"
                                    size="icon"
                                    class="size-11 rounded-2xl border-slate-200 bg-white text-slate-700 shadow-sm hover:bg-slate-50"
                                    @click="$emit('view', row)"
                                >
                                    <Eye class="size-4" />
                                </Button>
                                <Button
                                    type="button"
                                    variant="outline"
                                    size="icon"
                                    class="size-11 rounded-2xl border-slate-200 bg-white text-slate-700 shadow-sm hover:bg-slate-50"
                                    @click="$emit('edit', row)"
                                >
                                    <Pencil class="size-4" />
                                </Button>
                                <Button
                                    type="button"
                                    variant="outline"
                                    size="icon"
                                    class="size-11 rounded-2xl border-red-200 bg-white text-red-600 shadow-sm hover:bg-red-50"
                                    @click="$emit('delete', row)"
                                >
                                    <Trash2 class="size-4" />
                                </Button>
                            </div>
                        </td>
                    </tr>
                </tbody>
                <tbody v-if="rows.length === 0">
                    <tr class="border-t border-slate-200/80 text-sm text-slate-500">
                        <td colspan="5" class="px-5 py-10 text-center">
                            Belum ada order yang tersimpan.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
