<script setup>
import { ref } from "vue"

import { MoreHorizontal } from "lucide-vue-next"

defineProps({
  items: {
    type: Array,
    default: () => [],
  },
})

const emit = defineEmits(["select"])


const detailsEl = ref(null)

const pick = (key) => {
  emit("select", key)
  if (detailsEl.value) {
    detailsEl.value.removeAttribute('open')
  }
}

</script>

<template>
  <details ref="detailsEl" class="group relative">
    <summary class="list-none cursor-pointer p-2 hover:bg-slate-100 rounded-full transition-colors flex items-center justify-center h-10 w-10">
      <MoreHorizontal class="h-5 w-5 text-slate-500" />
    </summary>

    <div class="absolute right-0 z-40 mt-2 min-w-40 rounded-2xl border border-slate-200 bg-white p-2 shadow-xl shadow-slate-950/10">
      <button
        v-for="item in items"
        :key="item.key"
        type="button"
        :disabled="item.disabled"
        class="flex w-full items-center rounded-xl px-3 py-2 text-left text-sm transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50"
        :class="item.tone === 'danger' ? 'text-rose-600 hover:bg-rose-50' : 'text-slate-700'"
        @click="pick(item.key)"
      >
        {{ item.label }}
      </button>
    </div>
  </details>
</template>
