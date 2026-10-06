<script setup>
import { ref, computed, watch } from 'vue';
import {
    PopoverRoot, PopoverTrigger, PopoverContent, PopoverPortal,
    RangeCalendarRoot, RangeCalendarHeader, RangeCalendarHeading,
    RangeCalendarGrid, RangeCalendarGridHead, RangeCalendarGridRow,
    RangeCalendarHeadCell, RangeCalendarGridBody, RangeCalendarCell,
    RangeCalendarCellTrigger, RangeCalendarNext, RangeCalendarPrev,
} from 'radix-vue';
import { CalendarDate, parseDate, today, getLocalTimeZone } from '@internationalized/date';
import CalendarRangeIcon from '@lucide/vue/dist/esm/icons/calendar-range.mjs';
import ChevronLeft from '@lucide/vue/dist/esm/icons/chevron-left.mjs';
import ChevronRight from '@lucide/vue/dist/esm/icons/chevron-right.mjs';

const props = defineProps({
    startDate: { type: String, default: '' },
    endDate:   { type: String, default: '' },
    placeholder: { type: String, default: 'Pilih rentang tanggal' },
});

const emit = defineEmits(['update:startDate', 'update:endDate']);

const open = ref(false);

// Convert plain string "YYYY-MM-DD" ↔ CalendarDate
const toCalendarDate = (str) => {
    if (!str) return undefined;
    try { return parseDate(str); } catch { return undefined; }
};
const fromCalendarDate = (d) => {
    if (!d) return '';
    const pad = (n) => String(n).padStart(2, '0');
    return `${d.year}-${pad(d.month)}-${pad(d.day)}`;
};

// Internal range state for Radix
const range = ref({
    start: toCalendarDate(props.startDate),
    end:   toCalendarDate(props.endDate),
});

// Sync in from parent
watch(() => [props.startDate, props.endDate], ([s, e]) => {
    range.value = { start: toCalendarDate(s), end: toCalendarDate(e) };
});

// Sync out to parent
watch(range, (val) => {
    emit('update:startDate', fromCalendarDate(val?.start));
    emit('update:endDate',   fromCalendarDate(val?.end));
    if (val?.start && val?.end) open.value = false;
}, { deep: true });

// Display label
const label = computed(() => {
    if (props.startDate && props.endDate) {
        const fmt = (s) => {
            if (!s) return '';
            const [y, m, d] = s.split('-');
            return `${d}/${m}/${y}`;
        };
        return `${fmt(props.startDate)} – ${fmt(props.endDate)}`;
    }
    return props.placeholder;
});

const isSame = (a, b) => a && b && a.year === b.year && a.month === b.month && a.day === b.day;
</script>

<template>
    <PopoverRoot v-model:open="open">
        <PopoverTrigger as-child>
            <button
                class="flex items-center gap-2 h-10 rounded-md border border-slate-300 bg-white px-3 py-1 text-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 whitespace-nowrap"
                :class="{ 'text-slate-400': !startDate || !endDate, 'text-slate-800': startDate && endDate }"
            >
                <CalendarRangeIcon class="h-4 w-4 shrink-0" />
                {{ label }}
            </button>
        </PopoverTrigger>

        <PopoverPortal>
            <PopoverContent
                align="start"
                :side-offset="4"
                class="z-50 rounded-xl border border-slate-200 bg-white p-4 shadow-lg"
            >
                <RangeCalendarRoot
                    v-model="range"
                    :locale="'id-ID'"
                    class="w-full"
                    v-slot="{ weekDays, grid }"
                >
                    <RangeCalendarHeader class="flex items-center justify-between mb-3">
                        <RangeCalendarPrev as-child>
                            <button class="p-1 rounded hover:bg-slate-100">
                                <ChevronLeft class="h-4 w-4" />
                            </button>
                        </RangeCalendarPrev>
                        <RangeCalendarHeading class="text-sm font-semibold text-slate-800" />
                        <RangeCalendarNext as-child>
                            <button class="p-1 rounded hover:bg-slate-100">
                                <ChevronRight class="h-4 w-4" />
                            </button>
                        </RangeCalendarNext>
                    </RangeCalendarHeader>

                    <RangeCalendarGrid v-for="month in grid" :key="month.value.toString()">
                        <RangeCalendarGridHead>
                            <RangeCalendarGridRow class="grid grid-cols-7 mb-1">
                                <RangeCalendarHeadCell
                                    v-for="day in weekDays"
                                    :key="day"
                                    class="text-xs text-center text-slate-500 font-medium py-1"
                                >
                                    {{ day }}
                                </RangeCalendarHeadCell>
                            </RangeCalendarGridRow>
                        </RangeCalendarGridHead>

                        <RangeCalendarGridBody>
                            <RangeCalendarGridRow
                                v-for="(week, i) in month.rows"
                                :key="i"
                                class="grid grid-cols-7"
                            >
                                <RangeCalendarCell
                                    v-for="day in week"
                                    :key="day.toString()"
                                    :date="day"
                                >
                                    <RangeCalendarCellTrigger
                                        :day="day"
                                        :month="month.value"
                                        class="w-full h-8 text-xs flex items-center justify-center rounded-md transition-colors
                                            data-[today]:ring-1 data-[today]:ring-indigo-400
                                            data-[highlighted]:bg-indigo-100 data-[highlighted]:text-indigo-800
                                            data-[selected]:bg-indigo-600 data-[selected]:text-white data-[selected]:font-semibold
                                            data-[outside-month]:text-slate-300
                                            data-[disabled]:opacity-40 data-[disabled]:cursor-not-allowed
                                            hover:bg-slate-100 cursor-pointer"
                                    />
                                </RangeCalendarCell>
                            </RangeCalendarGridRow>
                        </RangeCalendarGridBody>
                    </RangeCalendarGrid>
                </RangeCalendarRoot>

                <!-- Footer label -->
                <div v-if="startDate" class="mt-3 pt-3 border-t border-slate-100 text-xs text-slate-500 flex justify-between">
                    <span>Dari: <strong class="text-slate-700">{{ startDate }}</strong></span>
                    <span v-if="endDate">Sampai: <strong class="text-slate-700">{{ endDate }}</strong></span>
                    <span v-else class="italic">Pilih tanggal akhir</span>
                </div>
            </PopoverContent>
        </PopoverPortal>
    </PopoverRoot>
</template>
