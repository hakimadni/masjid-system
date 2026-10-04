<script setup>
import { computed, ref, watch } from "vue"

const props = defineProps({
  open: { type: Boolean, required: true },
})

const emit = defineEmits(["update:open"])

const isOpen = computed({
  get: () => props.open,
  set: (value) => emit("update:open", value),
})
</script>

<template>
  <Teleport to="body">
    <transition enter-from-class="opacity-0" enter-active-class="transition-opacity duration-200" leave-to-class="opacity-0" leave-active-class="transition-opacity duration-200">
      <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="isOpen = false">
        <div class="w-full max-w-md rounded-lg bg-white shadow-xl">
          <slot :close="() => (isOpen = false)" />
        </div>
      </div>
    </transition>
  </Teleport>
</template>