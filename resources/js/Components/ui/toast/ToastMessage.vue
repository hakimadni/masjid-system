<script setup>
defineProps({
  message: { type: String, default: "" },
})
</script>

<template>
  <div v-if="message" class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-700">
    {{ message }}
  </div>
</template>
