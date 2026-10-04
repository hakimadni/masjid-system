<script setup>
import Card from "@/Components/ui/card/Card.vue"

defineProps({
  title: { type: String, required: true },
  value: { type: [String, Number], required: true },
  subtitle: { type: String, default: "" },
})
</script>

<template>
  <Card class="p-5">
    <p class="text-sm text-slate-500">{{ title }}</p>
    <p class="mt-2 text-2xl font-semibold text-slate-900">{{ value }}</p>
    <p v-if="subtitle" class="mt-1 text-xs text-slate-500">{{ subtitle }}</p>
  </Card>
</template>
