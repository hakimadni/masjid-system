<script setup>
defineProps({
  label: { type: String, required: true },
  token: { type: String, required: true },
})
</script>

<template>
  <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
    <p class="text-xs text-slate-500">{{ label }}</p>
    <p class="mt-1 break-all text-xs text-slate-700">{{ token }}</p>
  </div>
</template>
