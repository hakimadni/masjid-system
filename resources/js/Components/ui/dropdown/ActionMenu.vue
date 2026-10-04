<script setup>
import Button from "@/Components/ui/button/Button.vue"
import { MoreHorizontal } from "lucide-vue-next"

defineProps({
  items: {
    type: Array,
    default: () => [],
  },
})

const emit = defineEmits(["select"])

const pick = (key) => {
  emit("select", key)
}
</script>

<template>
  <details class="group relative">
    <summary class="list-none">
      <Button variant="ghost" size="icon" class="h-9 w-9">
        <MoreHorizontal class="h-4 w-4" />
      </Button>
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
