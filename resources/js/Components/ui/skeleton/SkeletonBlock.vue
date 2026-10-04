<script setup>
import { cn } from "@/lib/utils"

defineProps({
  class: { type: String, default: "" },
})
</script>

<template>
  <div :class="cn('animate-pulse rounded-xl bg-slate-200/80', $props.class)" />
</template>
