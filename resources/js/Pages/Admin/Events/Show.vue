<script setup>
import { Head, Link } from "@inertiajs/vue3"
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import Card from "@/Components/ui/card/Card.vue"
import StatusBadge from "@/Components/ui/status/StatusBadge.vue"
import Button from "@/Components/ui/button/Button.vue"

const props = defineProps({
  event: { type: Object, required: true },
})
</script>

<template>
  <Head title="Detail Kegiatan" />

  <AuthenticatedLayout title="Detail Kegiatan">
    <div class="space-y-6">
      <Card class="p-5">
        <div class="flex items-start justify-between">
          <div>
            <h2 class="text-lg font-semibold text-slate-900">{{ event.title }}</h2>
            <p class="mt-1 text-sm text-slate-500">{{ event.pic_name || 'Tanpa PIC' }}</p>
          </div>
          <StatusBadge :status="event.status" />
        </div>
        <div class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-3">
          <div>
            <p class="text-xs font-medium text-slate-500 uppercase">Lokasi</p>
            <p class="text-sm text-slate-700">{{ event.location || '-' }}</p>
          </div>
          <div>
            <p class="text-xs font-medium text-slate-500 uppercase">Waktu Mulai</p>
            <p class="text-sm text-slate-700">{{ event.start_at }}</p>
          </div>
          <div>
            <p class="text-xs font-medium text-slate-500 uppercase">Waktu Selesai</p>
            <p class="text-sm text-slate-700">{{ event.end_at || '-' }}</p>
          </div>
        </div>
        <div v-if="event.notes" class="mt-4">
          <p class="text-xs font-medium text-slate-500 uppercase">Catatan</p>
          <p class="text-sm text-slate-700">{{ event.notes }}</p>
        </div>
        <div class="mt-4">
          <Link :href="route('events.index')">
            <Button variant="outline" size="sm">Kembali ke Daftar</Button>
          </Link>
        </div>
      </Card>
    </div>
  </AuthenticatedLayout>
</template>