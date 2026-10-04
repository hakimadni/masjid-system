<script setup>
import { computed } from "vue"
import { cva } from "class-variance-authority"
import { cn } from "@/lib/utils"

const props = defineProps({
  variant: { type: String, default: "default" },
  class: { type: String, default: "" },
})

const badgeVariants = cva(
  "inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-semibold tracking-[0.01em]",
  {
  variants: {
    variant: {
      default: "border-slate-200 bg-slate-100 text-slate-700",
      success: "border-emerald-200 bg-emerald-50 text-emerald-700",
      warning: "border-amber-200 bg-amber-50 text-amber-700",
      danger: "border-rose-200 bg-rose-50 text-rose-700",
      info: "border-sky-200 bg-sky-50 text-sky-700",
      muted: "border-slate-200 bg-white text-slate-600",
    },
  },
})

const classes = computed(() => cn(badgeVariants({ variant: props.variant }), props.class))
</script>

<template>
  <span :class="classes"><slot /></span>
</template>
