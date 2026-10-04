<script setup>
import { cn } from "@/lib/utils"

defineProps({ class: { type: String, default: "" } })
</script>

<template>
  <h3 :class="cn('text-lg font-semibold text-slate-900', $props.class)">
    <slot />
  </h3>
</template>