<script setup>
import { computed } from "vue"
import Badge from "@/Components/ui/badge/Badge.vue"

const props = defineProps({
  status: { type: String, required: true },
})

const classes = computed(() => {
  const map = {
    available: "default",
    assigned: "info",
    slaughtered: "warning",
    distributed: "success",
  }

  return map[props.status] ?? "default"
})
</script>

<template>
  <Badge :variant="classes">
    {{ status }}
  </Badge>
</template>
