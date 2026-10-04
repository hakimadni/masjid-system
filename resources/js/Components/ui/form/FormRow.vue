<script setup>
defineProps({
  label: { type: String, required: true },
})
</script>

<template>
  <label class="block space-y-1">
    <span class="text-xs text-slate-600">{{ label }}</span>
    <slot />
  </label>
</template>
