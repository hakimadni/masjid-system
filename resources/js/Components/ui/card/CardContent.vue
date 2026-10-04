<script setup>
import { cn } from "@/lib/utils"

defineProps({ class: { type: String, default: "" } })
</script>

<template>
  <div :class="cn('px-6 py-4', $props.class)">
    <slot />
  </div>
</template>