<script setup>
defineProps({
  title: { type: String, required: true },
})
</script>

<template>
  <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
    <p class="mb-3 text-sm font-semibold text-slate-800">{{ title }}</p>
    <slot />
  </div>
</template>
