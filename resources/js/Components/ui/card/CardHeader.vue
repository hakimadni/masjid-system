<script setup>
import { cn } from "@/lib/utils"

defineProps({ class: { type: String, default: "" } })
</script>

<template>
  <div :class="cn('px-6 py-4 border-b border-slate-200', $props.class)">
    <slot />
  </div>
</template>