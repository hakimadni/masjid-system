<template>
  <div class="w-full">
    <!-- Mobile cards -->
    <div class="flex flex-col gap-3 md:hidden">
      <slot name="mobile-card" v-for="(item, index) in data" :key="item.id ?? index" :item="item" />
    </div>

    <!-- Desktop table -->
    <div class="hidden md:block overflow-hidden rounded-2xl border border-white/70 bg-white/95 shadow-sm shadow-slate-950/5 ring-1 ring-slate-200/70">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 text-sm text-slate-700">
          <slot />
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  data: {
    type: Array,
    default: () => [],
  }
});
</script>