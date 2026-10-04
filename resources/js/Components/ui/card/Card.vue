<script setup>
import { cn } from "@/lib/utils"

defineProps({ class: { type: String, default: "" } })
</script>

<template>
  <div :class="cn('rounded-xl border border-slate-200 bg-white text-slate-900 shadow-sm', $props.class)">
    <slot />
  </div>
</template>
