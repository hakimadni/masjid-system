<script setup>
import { cn } from "@/lib/utils"

defineProps({ class: { type: String, default: "" } })
</script>

<template>
  <div :class="cn('rounded-2xl border border-emerald-900/5 bg-white text-slate-900 shadow-xl shadow-emerald-900/5 ring-1 ring-slate-100/50', $props.class)">
    <slot />
  </div>
</template>
