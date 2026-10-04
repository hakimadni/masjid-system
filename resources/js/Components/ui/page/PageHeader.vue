<script setup>
const props = defineProps({
  title: { type: String, required: true },
  description: { type: String, default: "" },
})
</script>

<template>
  <div class="flex flex-col gap-2 md:flex-row md:items-start md:justify-between">
    <div>
      <h1 class="text-xl font-semibold text-slate-900 md:text-2xl">{{ title }}</h1>
      <p v-if="description" class="mt-1 text-sm text-slate-500">{{ description }}</p>
    </div>
    <div v-if="$slots.actions" class="flex items-center gap-2">
      <slot name="actions" />
    </div>
  </div>
</template>
