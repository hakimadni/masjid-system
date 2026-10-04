<script setup>
import { computed } from "vue"

const props = defineProps({
  value: { type: [Number, String], default: 0 },
  currency: { type: String, default: "IDR" },
  locale: { type: String, default: "id-ID" },
  maximumFractionDigits: { type: Number, default: 0 },
})

const normalizedValue = computed(() => Number(props.value ?? 0) || 0)

const formatted = computed(() =>
  new Intl.NumberFormat(props.locale, {
    style: "currency",
    currency: props.currency,
    maximumFractionDigits: props.maximumFractionDigits,
  }).format(normalizedValue.value)
)
</script>

<template>
  <span><slot :formatted="formatted">{{ formatted }}</slot></span>
</template>
