<script setup>
import { computed } from "vue"
import { Head, usePage } from "@inertiajs/vue3"
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import Card from "@/Components/ui/card/Card.vue"
import EmptyState from "@/Components/ui/empty/EmptyState.vue"
import EventsIndex from "@/Pages/Admin/Events/Index.vue"
import AnnouncementsIndex from "@/Pages/Admin/Announcements/Index.vue"
import AssetsIndex from "@/Pages/Admin/Assets/Index.vue"
import ReportsIndex from "@/Pages/Admin/Reports/Index.vue"

const props = defineProps({
  module: { type: Object, required: true },
})

const page = usePage()

const pageComponent = computed(() => {
  const component = page.component ?? ""

  if (component === "Admin/Modules/Placeholder") {
    const routeName = typeof route === "function" ? route().current() : ""

    if (routeName === "events.index") return EventsIndex
    if (routeName === "announcements.index") return AnnouncementsIndex
    if (routeName === "assets.index") return AssetsIndex
    if (routeName === "reports.index") return ReportsIndex
  }

  return null
})
</script>

<template>
  <component :is="pageComponent" v-if="pageComponent" v-bind="$page.props" :module="module" />

  <template v-else>
  <Head :title="module.label" />

  <AuthenticatedLayout :title="module.label">
    <div class="space-y-6">
      <Card class="p-5">
        <p class="text-lg font-semibold text-slate-900">{{ module.label }}</p>
        <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">{{ module.description }}</p>
      </Card>

      <div class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
        <Card class="p-5">
          <p class="text-sm font-semibold text-slate-900">Status Modul</p>
          <div class="mt-4 space-y-3 text-sm text-slate-600">
            <p>Struktur navigasi dan proteksi role sudah aktif.</p>
            <p>Jumlah data terdeteksi saat ini: <span class="font-semibold text-slate-900">{{ module.records }}</span>.</p>
            <p>Halaman ini disiapkan sebagai placeholder terproteksi agar ekspansi modul berikutnya tidak perlu mengubah layout utama lagi.</p>
          </div>
        </Card>

        <Card class="p-5">
          <p class="text-sm font-semibold text-slate-900">Langkah Selanjutnya</p>
          <div class="mt-4 space-y-3">
            <div v-for="step in module.next_steps" :key="step" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-600">
              {{ step }}
            </div>
          </div>
        </Card>
      </div>

      <EmptyState title="Modul Siap Dikembangkan" description="Fondasi UI, route, dan hak akses sudah aktif. Implementasi CRUD rinci bisa dilanjutkan tanpa mengubah struktur admin lagi." />
    </div>
  </AuthenticatedLayout>
  </template>
</template>
