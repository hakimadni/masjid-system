<template>
  <Modal :show="show" max-width="lg" @close="$emit('update:show', false)">
    <template #header>
      <div class="flex justify-end space-x-2">
        <PrimaryButton @click="approve">Approve</PrimaryButton>
        <SecondaryButton @click="reject">Reject</SecondaryButton>
      </div>
    </template>
    <div class="p-4">
      <slot />
    </div>
  </Modal>
</template>

<script>
import Modal from '@/Components/Modal.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'

export default {
  name: 'DetailModal',
  components: { Modal, PrimaryButton, SecondaryButton },
  props: {
    show: { type: Boolean, default: false },
    data: { type: Object, default: () => ({}) }
  },
  emits: ['update:show', 'approve', 'reject'],
  methods: {
    approve() { this.$emit('approve'); this.$emit('update:show', false) },
    reject() { this.$emit('reject'); this.$emit('update:show', false) }
  }
}
</script>

<style scoped>
/* optional modal styling overrides */
</style>