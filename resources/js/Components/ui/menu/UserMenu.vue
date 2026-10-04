<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from "vue"
import { Link } from "@inertiajs/vue3"
import { ChevronDown, LogOut, Settings, User2 } from "lucide-vue-next"

const props = defineProps({
  user: {
    type: Object,
    default: () => ({}),
  },
})

const open = ref(false)
const root = ref(null)

const initials = computed(() =>
  String(props.user?.name ?? "A")
    .split(" ")
    .map((part) => part[0] ?? "")
    .join("")
    .slice(0, 2)
    .toUpperCase()
)

const closeOnOutside = (event) => {
  if (!root.value?.contains(event.target)) {
    open.value = false
  }
}

onMounted(() => window.addEventListener("click", closeOnOutside))
onBeforeUnmount(() => window.removeEventListener("click", closeOnOutside))
</script>

<template>
  <div ref="root" class="relative">
    <button
      type="button"
      class="flex items-center gap-3 rounded-2xl border border-white/70 bg-white/90 px-3 py-2 text-left shadow-sm shadow-slate-950/5 transition hover:border-emerald-200 hover:bg-white"
      @click.stop="open = !open"
    >
      <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-100 text-xs font-semibold text-emerald-700">
        {{ initials }}
      </div>
      <div class="hidden min-w-0 sm:block">
        <p class="truncate text-sm font-semibold text-slate-900">{{ user?.name ?? "Pengguna" }}</p>
        <p class="truncate text-xs text-slate-500">{{ user?.role ?? "Administrator" }}</p>
      </div>
      <ChevronDown class="h-4 w-4 text-slate-400" />
    </button>

    <Transition
      enter-active-class="transition duration-150 ease-out"
      enter-from-class="translate-y-1 opacity-0"
      enter-to-class="translate-y-0 opacity-100"
      leave-active-class="transition duration-100 ease-in"
      leave-from-class="translate-y-0 opacity-100"
      leave-to-class="translate-y-1 opacity-0"
    >
      <div
        v-if="open"
        class="absolute right-0 top-[calc(100%+0.75rem)] z-50 w-64 rounded-2xl border border-slate-200 bg-white p-2 shadow-xl shadow-slate-950/10"
      >
        <div class="rounded-xl bg-slate-50 px-3 py-3">
          <p class="text-sm font-semibold text-slate-900">{{ user?.name ?? "Pengguna" }}</p>
          <p class="mt-1 text-xs text-slate-500">{{ user?.email ?? "Tidak ada email" }}</p>
        </div>

        <div class="mt-2 space-y-1">
          <Link
            :href="route('profile.edit')"
            class="flex items-center gap-2 rounded-xl px-3 py-2 text-sm text-slate-700 transition hover:bg-emerald-50 hover:text-emerald-900"
            @click="open = false"
          >
            <User2 class="h-4 w-4" />
            Profil Saya
          </Link>
          <Link
            :href="route('profile.edit')"
            class="flex items-center gap-2 rounded-xl px-3 py-2 text-sm text-slate-700 transition hover:bg-emerald-50 hover:text-emerald-900"
            @click="open = false"
          >
            <Settings class="h-4 w-4" />
            Pengaturan Akun
          </Link>
          <Link
            :href="route('logout')"
            method="post"
            as="button"
            class="flex w-full items-center gap-2 rounded-xl px-3 py-2 text-sm text-rose-600 transition hover:bg-rose-50"
            @click="open = false"
          >
            <LogOut class="h-4 w-4" />
            Keluar
          </Link>
        </div>
      </div>
    </Transition>
  </div>
</template>
