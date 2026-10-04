<script setup>
import { computed } from "vue"
import { Link, usePage } from "@inertiajs/vue3"
import { Menu } from "lucide-vue-next"
import { getVisibleNavigation, isNavItemOrChildActive } from "@/lib/adminNavigation"

const page = usePage()
const auth = computed(() => page.props.auth ?? {})
const currentRouteName = computed(() => route().current())
const currentPath = computed(() => page.url?.split("?")[0] ?? "")

const navigation = computed(() => getVisibleNavigation(auth.value))

// Take the first 4 visible navigation items for the bottom nav
const bottomNavItems = computed(() => {
  return navigation.value.slice(0, 4)
})

const isActive = (item) => isNavItemOrChildActive(item, currentRouteName.value, currentPath.value)

defineEmits(["openMenu"])
</script>

<template>
  <div
    class="fixed inset-x-0 bottom-0 z-40 border-t border-slate-200/60 bg-white/85 backdrop-blur-md md:hidden"
    style="padding-bottom: env(safe-area-inset-bottom);"
  >
    <div class="flex h-[4rem] items-center justify-around px-2">
      <Link
        v-for="item in bottomNavItems"
        :key="item.route"
        :href="route(item.route)"
        class="flex flex-col items-center justify-center gap-1 min-w-[4rem] px-1 py-1 transition-colors"
        :class="isActive(item) ? 'text-emerald-700' : 'text-slate-500 hover:text-slate-900'"
      >
        <component
          :is="item.icon"
          class="h-6 w-6 transition-transform"
          :class="isActive(item) ? 'scale-110' : ''"
        />
        <span
          class="text-[10px] truncate w-full text-center"
          :class="isActive(item) ? 'font-semibold' : 'font-medium'"
        >
          {{ item.label }}
        </span>
      </Link>
      
      <button
        type="button"
        @click="$emit('openMenu')"
        class="flex flex-col items-center justify-center gap-1 min-w-[4rem] px-1 py-1 text-slate-500 hover:text-slate-900 transition-colors"
      >
        <Menu class="h-6 w-6" />
        <span class="text-[10px] font-medium w-full text-center truncate">Menu</span>
      </button>
    </div>
  </div>
</template>
