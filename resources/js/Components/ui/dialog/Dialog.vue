<script setup>
import { computed, ref, watch } from "vue"

const props = defineProps({
  open: { type: Boolean, required: true },
})

const emit = defineEmits(["update:open", "close"])

const isOpen = computed({
  get: () => props.open,
  set: (value) => { emit("update:open", value); if (!value) emit("close"); },
})
</script>

<template>
  <Teleport to="body">
    <transition enter-from-class="opacity-0" enter-active-class="transition-opacity duration-200" leave-to-class="opacity-0" leave-active-class="transition-opacity duration-200">
      <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="isOpen = false">
        <div class="w-[92vw] sm:max-w-md max-h-[90vh] overflow-y-auto rounded-2xl bg-white shadow-2xl relative flex flex-col">
          <!-- Close button -->
          <button @click="isOpen = false" class="absolute top-4 right-4 z-50 rounded-full bg-slate-100 p-2 text-slate-500 hover:bg-slate-200 active:scale-95 transition-all">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
          </button>
          <slot :close="() => (isOpen = false)" />
        </div>

      </div>
    </transition>
  </Teleport>
</template>