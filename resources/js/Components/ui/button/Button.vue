<script setup>
import { computed } from "vue"
import { cva } from "class-variance-authority"
import { cn } from "@/lib/utils"

const props = defineProps({
  as: { type: String, default: "button" },
  type: { type: String, default: "button" },
  variant: { type: String, default: "default" },
  size: { type: String, default: "default" },
  class: { type: String, default: "" },
  disabled: { type: Boolean, default: false },
})

const buttonVariants = cva(
  "inline-flex items-center justify-center gap-2 rounded-xl text-sm font-medium transition-all duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50",
  {
    variants: {
      variant: {
        default:
          "bg-emerald-600 text-white shadow-sm shadow-emerald-950/10 hover:bg-emerald-500 focus-visible:ring-emerald-500",
        outline:
          "border border-slate-200 bg-white text-slate-700 shadow-sm hover:border-emerald-200 hover:bg-emerald-50/70 hover:text-emerald-900 focus-visible:ring-emerald-300",
        secondary:
          "bg-slate-100 text-slate-700 hover:bg-slate-200/80 focus-visible:ring-slate-300",
        success:
          "bg-emerald-100 text-emerald-800 hover:bg-emerald-200 focus-visible:ring-emerald-300",
        ghost:
          "text-slate-600 hover:bg-white/80 hover:text-slate-900 focus-visible:ring-slate-300",
      },
      size: {
        default: "h-10 px-4 py-2.5",
        sm: "h-8 rounded-lg px-3 text-xs",
        lg: "h-11 px-6",
        icon: "h-10 w-10",
      },
    },
  }
)

const classes = computed(() => cn(buttonVariants({ variant: props.variant, size: props.size }), props.class))
</script>

<template>
  <component :is="as" :type="type" :class="classes" :disabled="disabled">
    <slot />
  </component>
</template>
