<script setup>
import { Inbox } from "lucide-vue-next"

defineProps({
  title: { type: String, default: "Belum ada data" },
  description: { type: String, default: "Data akan muncul di sini setelah tersedia." },
})
</script>

<template>
  <div class="rounded-3xl border border-dashed border-slate-300 bg-white/75 px-6 py-12 text-center shadow-sm">
    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
      <Inbox class="h-6 w-6" />
    </div>
    <h3 class="mt-4 text-base font-semibold text-slate-900">{{ title }}</h3>
    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">{{ description }}</p>
    <div v-if="$slots.default" class="mt-5">
      <slot />
    </div>
  </div>
</template>
