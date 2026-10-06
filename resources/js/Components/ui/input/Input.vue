<script setup>
import { cn } from "@/lib/utils"

defineProps({ class: { type: String, default: "" } })
const model = defineModel({ type: [String, Number], default: "" })
</script>

<template>
  <input
    v-model="model"
    :class="cn('h-10 w-full max-w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-400', $props.class)"
  />
</template>
