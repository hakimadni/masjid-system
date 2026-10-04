<script setup>
import { Head, Link } from "@inertiajs/vue3"
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import Card from "@/Components/ui/card/Card.vue"
import StatusBadge from "@/Components/ui/status/StatusBadge.vue"
import Button from "@/Components/ui/button/Button.vue"

const props = defineProps({
  asset: { type: Object, required: true },
})
</script>

<template>
  <Head title="Detail Inventaris" />

  <AuthenticatedLayout title="Detail Inventaris">
    <div class="space-y-6">
      <Card class="p-5">
        <div class="flex items-start justify-between">
          <div>
            <h2 class="text-lg font-semibold text-slate-900">{{ asset.name }}</h2>
            <p class="mt-1 text-sm text-slate-500">{{ asset.category || 'Tanpa kategori' }}</p>
          </div>
          <StatusBadge :status="asset.status" />
        </div>

        <div class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-4">
          <div>
            <p class="text-xs font-medium text-slate-500 uppercase">Lokasi</p>
            <p class="text-sm text-slate-700">{{ asset.location || '-' }}</p>
          </div>
          <div>
            <p class="text-xs font-medium text-slate-500 uppercase">Jumlah</p>
            <p class="text-sm text-slate-700">{{ asset.quantity }} unit</p>
          </div>
          <div>
            <p class="text-xs font-medium text-slate-500 uppercase">Kondisi</p>
            <p class="text-sm text-slate-700">{{ asset.condition }}</p>
          </div>
          <div>
            <p class="text-xs font-medium text-slate-500 uppercase">Status</p>
            <p class="text-sm text-slate-700">{{ asset.status }}</p>
          </div>
        </div>

        <div v-if="asset.notes" class="mt-4">
          <p class="text-xs font-medium text-slate-500 uppercase">Catatan</p>
          <p class="text-sm text-slate-700">{{ asset.notes }}</p>
        </div>

        <div class="mt-4">
          <Link :href="route('assets.index')">
            <Button variant="outline" size="sm">Kembali ke Daftar</Button>
          </Link>
        </div>
      </Card>
    </div>
  </AuthenticatedLayout>
</template>