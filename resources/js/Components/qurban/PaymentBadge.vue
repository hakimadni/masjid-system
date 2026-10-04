<script setup>
import { computed } from "vue"
import Badge from "@/Components/ui/badge/Badge.vue"

const props = defineProps({
  status: { type: String, required: true },
})

const classes = computed(() => {
  if (props.status === "paid") return "success"
  if (props.status === "partial") return "warning"
  return "danger"
})
</script>

<template>
  <Badge :variant="classes">
    {{ status }}
  </Badge>
</template>
