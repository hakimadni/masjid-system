<template>
  <div @click="open" class="card cursor-pointer">
    <slot />
  </div>
</template>

<script>
export default {
  name: 'ClickableCard',
  methods: {
    open() {
      this.$emit('open')
    }
  }
}
</script>

<style scoped>
.card {
  /* optional styling, inherit existing card styles */
  display: flex;
  flex-direction: column;
  padding: 1rem;
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  background-color: #fff;
}
</style>