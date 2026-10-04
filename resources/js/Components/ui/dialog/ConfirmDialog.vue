<script setup>
import Dialog from "@/Components/ui/dialog/Dialog.vue"
import Button from "@/Components/ui/button/Button.vue"

const props = defineProps({
  open: { type: Boolean, required: true },
  title: { type: String, default: "Konfirmasi Tindakan" },
  description: { type: String, default: "Apakah Anda yakin ingin melanjutkan tindakan ini?" },
  confirmText: { type: String, default: "Lanjutkan" },
  cancelText: { type: String, default: "Batal" },
  processing: { type: Boolean, default: false },
  danger: { type: Boolean, default: false },
})

const emit = defineEmits(["update:open", "confirm", "cancel"])

const close = () => {
  emit("update:open", false)
  emit("cancel")
}

const confirm = () => {
  emit("confirm")
}
</script>

<template>
  <Dialog :open="open" @update:open="(value) => emit('update:open', value)">
    <div class="w-full max-w-md rounded-lg bg-white p-6">
      <h3 class="text-lg font-semibold text-slate-900">{{ title }}</h3>
      <p class="mt-2 text-sm text-slate-600">{{ description }}</p>

      <div v-if="$slots.default" class="mt-4">
        <slot />
      </div>

      <div class="mt-5 flex justify-end gap-2">
        <Button variant="outline" :disabled="processing" @click="close">{{ cancelText }}</Button>
        <Button :variant="danger ? 'secondary' : 'default'" :class="danger ? 'bg-rose-600 text-white hover:bg-rose-500 focus-visible:ring-rose-400' : ''" :disabled="processing" @click="confirm">
          {{ processing ? 'Memproses...' : confirmText }}
        </Button>
      </div>
    </div>
  </Dialog>
</template>
