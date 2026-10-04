<script setup>
import { ChevronRight } from "lucide-vue-next"

defineProps({
  items: {
    type: Array,
    default: () => [],
  },
})
</script>

<template>
  <nav aria-label="Breadcrumb" class="flex items-center gap-1 overflow-x-auto text-xs font-medium text-slate-500">
    <template v-for="(item, index) in items" :key="`${item.label}-${index}`">
      <span :class="item.current ? 'text-slate-900' : ''">{{ item.label }}</span>
      <ChevronRight v-if="index < items.length - 1" class="h-3.5 w-3.5 shrink-0 text-slate-400" />
    </template>
  </nav>
</template>
