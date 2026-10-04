<script setup>
import { computed } from "vue"
import Badge from "@/Components/ui/badge/Badge.vue"

const props = defineProps({
  status: { type: String, default: "" },
  label: { type: String, default: "" },
})

const normalizedStatus = computed(() => String(props.status ?? "").trim().toLowerCase())

const variant = computed(() => {
  if (["approved", "confirmed", "published", "active", "paid", "delivered", "success"].includes(normalizedStatus.value)) return "success"
  if (["pending", "draft", "process", "processing", "queued", "new"].includes(normalizedStatus.value)) return "warning"
  if (["cancelled", "canceled", "failed", "rejected", "inactive"].includes(normalizedStatus.value)) return "danger"
  if (["completed"].includes(normalizedStatus.value)) return "info"
  if (["archived"].includes(normalizedStatus.value)) return "muted"
  return "info"
})

const text = computed(() => props.label || props.status || "Unknown")
</script>

<template>
  <Badge :variant="variant">
    {{ text }}
  </Badge>
</template>
