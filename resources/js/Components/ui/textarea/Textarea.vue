<script setup>
import { cn } from "@/lib/utils"

defineProps({
  class: { type: String, default: "" },
  rows: { type: [String, Number], default: 4 },
})

const model = defineModel({ type: String, default: "" })
</script>

<template>
  <textarea
    v-model="model"
    :rows="rows"
    :class="cn('min-h-[96px] w-full max-w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-300', $props.class)"
  />
</template>
