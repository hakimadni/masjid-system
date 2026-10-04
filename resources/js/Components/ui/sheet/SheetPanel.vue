<script setup>
import { onBeforeUnmount, watch } from "vue"
import { X } from "lucide-vue-next"
import { cn } from "@/lib/utils"

const props = defineProps({
  open: { type: Boolean, default: false },
  title: { type: String, default: "" },
  description: { type: String, default: "" },
  class: { type: String, default: "" },
})

const emit = defineEmits(["update:open"])

const close = () => emit("update:open", false)

watch(
  () => props.open,
  (value) => {
    document.body.classList.toggle("overflow-hidden", value)
  }
)

onBeforeUnmount(() => {
  document.body.classList.remove("overflow-hidden")
})
</script>

<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition-opacity duration-200"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity duration-150"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="open" class="fixed inset-0 z-40 bg-slate-950/30 backdrop-blur-sm" @click="close" />
    </Transition>

    <Transition
      enter-active-class="transform transition duration-200 ease-out"
      enter-from-class="-translate-x-full"
      enter-to-class="translate-x-0"
      leave-active-class="transform transition duration-150 ease-in"
      leave-from-class="translate-x-0"
      leave-to-class="-translate-x-full"
    >
      <aside
        v-if="open"
        :class="
          cn(
            'fixed inset-y-0 left-0 z-50 flex w-[88vw] max-w-sm flex-col border-r border-white/60 bg-[#f7faf7] shadow-2xl shadow-emerald-950/10',
            props.class
          )
        "
      >
        <div class="flex items-start justify-between border-b border-slate-200/80 px-5 py-4">
          <div>
            <p v-if="title" class="text-sm font-semibold text-slate-900">{{ title }}</p>
            <p v-if="description" class="mt-1 text-xs text-slate-500">{{ description }}</p>
          </div>
          <button
            type="button"
            class="rounded-xl border border-slate-200 bg-white p-2 text-slate-500 transition hover:text-slate-900"
            @click="close"
          >
            <X class="h-4 w-4" />
          </button>
        </div>

        <div class="min-h-0 flex-1 overflow-y-auto">
          <slot />
        </div>
      </aside>
    </Transition>
  </Teleport>
</template>
