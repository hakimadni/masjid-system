<script setup>
import { computed, ref, useSlots } from "vue"
import { Link, usePage } from "@inertiajs/vue3"
import { ChevronRight, ChevronDown, Menu, Search } from "lucide-vue-next"
import Button from "@/Components/ui/button/Button.vue"
import Breadcrumbs from "@/Components/ui/navigation/Breadcrumbs.vue"
import SheetPanel from "@/Components/ui/sheet/SheetPanel.vue"
import UserMenu from "@/Components/ui/menu/UserMenu.vue"
import ApplicationLogo from "@/Components/ApplicationLogo.vue"
import {
  buildBreadcrumbs,
  findCurrentNavItem,
  findParentNavItem,
  getVisibleNavigation,
  isNavItemActive,
  isNavItemOrChildActive,
} from "@/lib/adminNavigation"

import MobileBottomNav from "@/Components/MobileBottomNav.vue"
import GlobalToast from "@/Components/ui/toast/GlobalToast.vue"

const props = defineProps({
  title: { type: String, default: "" },
})

const slots = useSlots()
const page = usePage()
const mobileOpen = ref(false)

const auth = computed(() => page.props.auth ?? {})
const user = computed(() => auth.value.user ?? {})
const activeMosque = computed(() => auth.value.active_mosque ?? null)
const currentPath = computed(() => page.url?.split("?")[0] ?? "")
const currentRouteName = computed(() => route().current())
const navigation = computed(() => getVisibleNavigation(auth.value))
const currentItem = computed(() => findCurrentNavItem(auth.value, currentRouteName.value, currentPath.value))
const parentItem = computed(() => findParentNavItem(auth.value, currentRouteName.value, currentPath.value))
const pageTitle = computed(() => props.title || currentItem.value?.label || "Admin Panel")
const breadcrumbs = computed(() =>
  buildBreadcrumbs({
    currentItem: currentItem.value,
    parentItem: parentItem.value,
    title: props.title || currentItem.value?.label,
  })
)

const isActive = (item) => isNavItemOrChildActive(item, currentRouteName.value, currentPath.value)
const isChildActive = (child) => isNavItemActive(child, currentRouteName.value, currentPath.value)
const childHref = (child) => route(child.route, child.tab ? { tab: child.tab } : undefined)
const expandedGroups = ref(new Set())
</script>

<template>
  <div class="admin-shell min-h-screen">
    <div class="pointer-events-none fixed inset-x-0 top-0 z-0 h-72 bg-[radial-gradient(circle_at_top,_rgba(74,222,128,0.16),_transparent_48%)]" />

    <div class="relative z-10 flex min-h-screen">
      <aside class="hidden w-80 shrink-0 px-5 py-5 xl:block">
        <div class="sticky top-5 flex h-[calc(100vh-2.5rem)] flex-col overflow-hidden rounded-[2rem] border border-white/70 bg-white/85 shadow-xl shadow-emerald-950/10 backdrop-blur">
          <div class="border-b border-slate-200/80 px-6 py-6">
            <div class="flex items-center gap-4">
              <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700">
                <ApplicationLogo class="h-8 w-8" />
              </div>
              <div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-emerald-700">MasjidOS</p>
                <p class="mt-1 text-lg font-semibold text-slate-900">Admin Masjid</p>
              </div>
            </div>

            <div class="mt-5 rounded-2xl border border-emerald-200/50 bg-gradient-to-br from-emerald-50 to-teal-50/50 px-4 py-3 shadow-inner">
              <p class="text-xs font-medium uppercase tracking-[0.2em] text-emerald-700">Ruang Kerja</p>
              <p class="mt-1 text-sm leading-6 text-slate-600">Operasional masjid yang rapi, tenang, dan siap dipakai cepat setiap hari.</p>
            </div>
          </div>

          <div class="min-h-0 flex-1 overflow-y-auto px-4 py-4">
            <nav class="space-y-2">
              <template v-for="item in navigation" :key="item.route">
                <Link
                  v-if="!item.children"
                  :href="route(item.route)"
                  :class="[
                    'group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm transition-all duration-200',
                    isActive(item)
                      ? 'bg-slate-900 text-white shadow-lg shadow-slate-900/10'
                      : 'text-slate-600 hover:bg-emerald-50 hover:text-slate-900',
                  ]"
                >
                  <span
                    :class="[
                      'flex h-10 w-10 items-center justify-center rounded-xl border transition',
                      isActive(item)
                        ? 'border-white/15 bg-white/10 text-white'
                        : 'border-slate-200 bg-white text-slate-500 group-hover:border-emerald-100 group-hover:bg-emerald-100/70 group-hover:text-emerald-700',
                    ]"
                  >
                    <component :is="item.icon" class="h-4 w-4" />
                  </span>
                  <div class="min-w-0 flex-1">
                    <p class="truncate font-medium">{{ item.label }}</p>
                    <p
                      :class="[
                        'truncate text-xs',
                        isActive(item) ? 'text-white/70' : 'text-slate-400 group-hover:text-emerald-700/80',
                      ]"
                    >
                      {{ item.group }}
                    </p>
                  </div>
                  <ChevronRight :class="[isActive(item) ? 'text-white/70' : 'text-slate-300']" class="h-4 w-4" />
                </Link>

                <div v-else class="group">
                  <button
                    type="button"
                    @click="expandedGroups.has(item.route) ? expandedGroups.delete(item.route) : expandedGroups.add(item.route)"
                    class="flex items-center gap-3 w-full rounded-2xl px-4 py-3 text-sm transition-all duration-200 text-slate-600 hover:bg-emerald-50 hover:text-slate-900"
                    :class="isActive(item) ? 'bg-slate-900 text-white' : ''"
                    :aria-expanded="expandedGroups.has(item.route)"
                  >
                    <span
                      :class="[
                        'flex h-10 w-10 items-center justify-center rounded-xl border transition',
                        isActive(item)
                          ? 'border-white/15 bg-white/10 text-white'
                          : 'border-slate-200 bg-white text-slate-500 group-hover:border-emerald-100 group-hover:bg-emerald-100/70 group-hover:text-emerald-700',
                      ]"
                    >
                      <component :is="item.icon" class="h-4 w-4" />
                    </span>
                    <div class="min-w-0 flex-1">
                      <p class="truncate font-medium">{{ item.label }}</p>
                      <p
                        :class="[
                          'truncate text-xs',
                          isActive(item) ? 'text-white/70' : 'text-slate-400 group-hover:text-emerald-700/80',
                        ]"
                      >
                        {{ item.group }}
                      </p>
                    </div>
                    <ChevronDown
                      :class="[
                        'h-4 w-4 transition-transform duration-200',
                        expandedGroups.has(item.route) ? 'rotate-180' : '',
                        isActive(item) ? 'text-white/70' : 'text-slate-300',
                      ]"
                    />
                  </button>

                  <Transition
                    enter-active-class="transition-all duration-200 ease-out"
                    enter-from-class="opacity-0 -translate-y-1 max-h-0"
                    enter-to-class="opacity-100 translate-y-0 max-h-60"
                    leave-active-class="transition-all duration-150 ease-in"
                    leave-from-class="opacity-100 translate-y-0 max-h-60"
                    leave-to-class="opacity-0 -translate-y-1 max-h-0"
                  >
                    <div v-show="expandedGroups.has(item.route)" class="overflow-hidden mt-1 ml-10 border-l border-slate-200/50 pl-3 space-y-1">
                      <Link
                        v-for="child in item.children"
                        :key="child.route"
                        :href="childHref(child)"
                        :class="[
                          'flex items-center gap-2 rounded-xl px-3 py-2 text-xs transition',
                          isChildActive(child)
                            ? 'bg-slate-900 text-white'
                            : 'text-slate-600 hover:bg-emerald-50 hover:text-slate-900',
                        ]"
                      >
                        <component :is="child.icon" class="h-3.5 w-3.5" />
                        <span class="truncate font-medium">{{ child.label }}</span>
                      </Link>
                    </div>
                  </Transition>
                </div>
              </template>
            </nav>
          </div>

          <div class="border-t border-slate-200/80 p-4">
            <div class="rounded-2xl bg-slate-50 px-4 py-4">
              <p class="text-xs font-medium uppercase tracking-[0.2em] text-slate-500">Login Sebagai</p>
              <p class="mt-2 text-sm font-semibold text-slate-900">{{ user.name ?? "Administrator" }}</p>
              <p class="mt-1 text-xs text-slate-500">{{ user.email ?? "Tidak ada email" }}</p>
              <p v-if="activeMosque?.name" class="mt-1 text-xs text-emerald-700">{{ activeMosque.name }}</p>
            </div>
          </div>
        </div>
      </aside>

      <div class="flex min-h-screen min-w-0 flex-1 flex-col px-3 pb-[calc(5rem+env(safe-area-inset-bottom))] md:pb-4 pt-3 sm:px-4 lg:px-6">
        <header class="sticky top-3 z-30">
          <div class="rounded-[2rem] border border-emerald-900/5 bg-white/80 shadow-xl shadow-emerald-900/5 backdrop-blur-xl ring-1 ring-white">
            <div class="flex items-center gap-3 px-4 py-3 sm:px-5 lg:px-6">
              <div class="flex flex-1 items-center gap-3">
                <Button variant="outline" size="icon" class="xl:hidden" @click="mobileOpen = true">
                  <Menu class="h-4 w-4" />
                </Button>

                <div class="hidden min-w-0 flex-1 items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2.5 lg:flex">
                  <Search class="h-4 w-4 text-slate-400" />
                  <span class="truncate text-sm text-slate-400">Pencarian modul, data peserta, atau transaksi</span>
                </div>

                <div class="min-w-0 flex-1 lg:hidden">
                  <p class="truncate text-sm font-semibold text-slate-900">MasjidOS</p>
                  <p class="truncate text-xs text-slate-500">{{ pageTitle }}</p>
                </div>
              </div>

              <div class="flex items-center gap-2 sm:gap-3">
                <slot name="topbar-actions" />
                <slot name="actions" />
                <UserMenu :user="user" />
              </div>
            </div>

            <div class="border-t border-slate-200/80 px-4 py-4 sm:px-5 lg:px-6">
              <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div class="min-w-0">
                  <slot name="breadcrumb">
                    <Breadcrumbs :items="breadcrumbs" />
                  </slot>

                  <div class="mt-2">
                    <div v-if="slots.header" class="text-xl font-semibold tracking-tight text-slate-950 sm:text-2xl">
                      <slot name="header" />
                    </div>
                    <h1 v-else class="text-xl font-semibold tracking-tight text-slate-950 sm:text-2xl">
                      {{ pageTitle }}
                    </h1>
                  </div>
                </div>

                <div v-if="slots.toolbar" class="flex shrink-0 items-center gap-2">
                  <slot name="toolbar" />
                </div>
              </div>
            </div>
          </div>
        </header>

        <main class="flex-1 px-1 pb-6 pt-5 sm:pt-6">
          <slot />
        </main>

        <footer class="flex flex-wrap items-center justify-between gap-2 border-t border-slate-200/60 px-4 py-3 text-xs text-slate-400">
          <p>
            <span class="font-semibold text-slate-500">MasjidOS</span>
            &copy; {{ new Date().getFullYear() }}
          </p>
          <div class="flex items-center gap-3">
            <span>v1.0.0</span>
            <span class="inline-block h-1 w-1 rounded-full bg-slate-300" />
            <span>{{ activeMosque?.name ?? 'Bendahara DKM' }}</span>
          </div>
        </footer>
      </div>
    </div>
    
    <MobileBottomNav @open-menu="mobileOpen = true" />

    <SheetPanel
      :open="mobileOpen"
      title="Navigasi Admin"
      description="Pindah modul operasional dengan cepat."
      @update:open="mobileOpen = $event"
    >
      <div class="space-y-2 p-4">
        <template v-for="item in navigation" :key="item.route">
          <Link
            v-if="!item.children"
            :href="route(item.route)"
            :class="[
              'flex items-center gap-3 rounded-2xl px-4 py-3 text-sm transition',
              isActive(item) ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 hover:bg-emerald-50 hover:text-slate-900',
            ]"
            @click="mobileOpen = false"
          >
            <span
              :class="[
                'flex h-10 w-10 items-center justify-center rounded-xl border',
                isActive(item) ? 'border-white/20 bg-white/10' : 'border-slate-200 bg-slate-50 text-slate-500',
              ]"
            >
              <component :is="item.icon" class="h-4 w-4" />
            </span>
            <div class="min-w-0 flex-1">
              <p class="truncate font-medium">{{ item.label }}</p>
              <p :class="[isActive(item) ? 'text-white/70' : 'text-slate-400']" class="truncate text-xs">{{ item.group }}</p>
            </div>
          </Link>

          <div v-else class="space-y-1">
            <div
              class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm transition"
              :class="isActive(item) ? 'bg-slate-900 text-white' : 'bg-white text-slate-600'"
            >
              <span
                :class="[
                  'flex h-10 w-10 items-center justify-center rounded-xl border',
                  isActive(item) ? 'border-white/20 bg-white/10' : 'border-slate-200 bg-slate-50 text-slate-500',
                ]"
              >
                <component :is="item.icon" class="h-4 w-4" />
              </span>
              <div class="min-w-0 flex-1">
                <p class="truncate font-medium">{{ item.label }}</p>
                <p class="truncate text-xs text-slate-400">{{ item.group }}</p>
              </div>
            </div>
            <div class="ml-10 border-l border-slate-200/50 pl-3 space-y-1">
              <Link
                v-for="child in item.children"
                :key="child.route"
                :href="childHref(child)"
                :class="[
                  'flex items-center gap-2 rounded-xl px-3 py-2 text-xs transition',
                  isChildActive(child)
                    ? 'bg-slate-900 text-white'
                    : 'bg-white text-slate-600 hover:bg-emerald-50 hover:text-slate-900',
                ]"
                @click="mobileOpen = false"
              >
                <component :is="child.icon" class="h-3.5 w-3.5" />
                <span class="truncate font-medium">{{ child.label }}</span>
              </Link>
            </div>
          </div>
        </template>
      </div>
    </SheetPanel>
  </div>
</template>
