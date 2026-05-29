<script setup lang="ts">
import { computed, ref } from 'vue';
import { CalendarDate, parseDate } from '@internationalized/date';
import { ChevronLeft, ChevronRight, Calendar as CalendarIcon } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
import {
    CalendarCell,
    CalendarCellTrigger,
    CalendarGrid,
    CalendarGridBody,
    CalendarGridHead,
    CalendarGridRow,
    CalendarHeadCell,
    CalendarHeader,
    CalendarHeading,
    CalendarNext,
    CalendarPrev,
    CalendarRoot,
} from 'reka-ui';

const props = defineProps<{
    modelValue: string;
    disabled?: boolean;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void;
}>();

const open = ref(false);

const defaultCalendarDate = () => parseDate(
    new Intl.DateTimeFormat('sv-SE', {
        timeZone: 'Asia/Jakarta',
    }).format(new Date()),
);

const calendarValue = computed({
    get() {
        return props.modelValue ? parseDate(props.modelValue) : defaultCalendarDate();
    },
    set(value) {
        emit('update:modelValue', value ? value.toString() : '');
        open.value = false;
    },
});

const displayLabel = computed(() => {
    if (!props.modelValue) {
        return 'Select date';
    }

    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    }).format(new Date(`${props.modelValue}T00:00:00`));
});

const minDate = new CalendarDate(2020, 1, 1);
const maxDate = new CalendarDate(2035, 12, 31);
</script>

<template>
    <Popover v-model:open="open">
        <PopoverTrigger as-child>
            <Button
                type="button"
                variant="outline"
                :disabled="disabled"
                class="h-12 w-full justify-between rounded-2xl border-slate-200 bg-white px-4 text-sm font-normal text-slate-700 shadow-sm hover:bg-slate-50"
            >
                <span class="truncate">{{ displayLabel }}</span>
                <CalendarIcon class="size-4 shrink-0 opacity-50" />
            </Button>
        </PopoverTrigger>

        <PopoverContent class="w-auto p-0">
            <CalendarRoot
                v-model="calendarValue"
                :min-value="minDate"
                :max-value="maxDate"
                :fixed-weeks="true"
                :initial-focus="true"
                class="rounded-2xl bg-white p-4 shadow-lg"
            >
                <template #default="{ grid, weekDays }">
                    <CalendarHeader class="flex items-center justify-between gap-2 px-1 pb-3">
                        <CalendarPrev as-child>
                            <Button
                                type="button"
                                variant="outline"
                                size="icon"
                                class="size-9 rounded-xl border-slate-200 bg-white text-slate-600 hover:bg-slate-50"
                            >
                                <ChevronLeft class="size-4" />
                            </Button>
                        </CalendarPrev>

                        <CalendarHeading class="text-sm font-semibold text-slate-950" />

                        <CalendarNext as-child>
                            <Button
                                type="button"
                                variant="outline"
                                size="icon"
                                class="size-9 rounded-xl border-slate-200 bg-white text-slate-600 hover:bg-slate-50"
                            >
                                <ChevronRight class="size-4" />
                            </Button>
                        </CalendarNext>
                    </CalendarHeader>

                    <div class="space-y-4">
                        <CalendarGrid
                            v-for="month in grid"
                            :key="month.value.toString()"
                            class="w-full border-collapse"
                        >
                            <CalendarGridHead>
                                <CalendarGridRow>
                                    <CalendarHeadCell
                                        v-for="day in weekDays"
                                        :key="day"
                                        class="w-10 pb-2 text-center text-xs font-medium text-slate-500"
                                    >
                                        {{ day }}
                                    </CalendarHeadCell>
                                </CalendarGridRow>
                            </CalendarGridHead>

                            <CalendarGridBody>
                                <CalendarGridRow
                                    v-for="(week, weekIndex) in month.rows"
                                    :key="weekIndex"
                                >
                                    <CalendarCell
                                        v-for="day in week"
                                        :key="day.toString()"
                                        :date="day"
                                        class="p-0 text-center"
                                    >
                                        <CalendarCellTrigger
                                            :day="day"
                                            :month="month.value"
                                            class="size-9 rounded-xl text-sm font-medium text-slate-700 transition hover:bg-red-50 hover:text-red-700 data-[selected]:bg-red-600 data-[selected]:text-white data-[today]:ring-1 data-[today]:ring-red-300 data-[outside-view]:text-slate-300"
                                        />
                                    </CalendarCell>
                                </CalendarGridRow>
                            </CalendarGridBody>
                        </CalendarGrid>
                    </div>
                </template>
            </CalendarRoot>
        </PopoverContent>
    </Popover>
</template>
