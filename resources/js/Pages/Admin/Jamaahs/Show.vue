<script setup>
import { Head, Link } from "@inertiajs/vue3"
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import Card from "@/Components/ui/card/Card.vue"
import Button from "@/Components/ui/button/Button.vue"
import StatusBadge from "@/Components/ui/status/StatusBadge.vue"
import PageHeader from "@/Components/ui/page/PageHeader.vue"

const props = defineProps({
  jamaah: { type: Object, required: true },
})

const detailRows = [
  ["Nama", props.jamaah.name],
  ["Kategori", props.jamaah.category],
  ["Jenis Kelamin", props.jamaah.gender === 'male' ? 'Laki-laki' : (props.jamaah.gender === 'female' ? 'Perempuan' : '-')],
  ["Telepon", props.jamaah.phone ?? "-"],
  ["Email", props.jamaah.email ?? "-"],
  ["Tanggal Lahir", props.jamaah.birth_date ?? "-"],
  ["Peran Keluarga", props.jamaah.family_role ?? "-"],
  ["Status", props.jamaah.status],
]
</script>

<template>
  <Head :title="`Detail Jamaah - ${jamaah.name}`" />

  <AuthenticatedLayout title="Detail Jamaah">
    <div class="space-y-6">
      <PageHeader :title="jamaah.name" :description="`Detail informasi jamaah`">
        <template #actions>
          <Link :href="route('jamaahs.index')"><Button variant="outline">Kembali</Button></Link>
        </template>
      </PageHeader>

      <div v-if="jamaah.is_masked" class="rounded-2xl border border-yellow-200 bg-yellow-50 px-4 py-3 text-sm text-yellow-800">
        Beberapa informasi kontak disembunyikan karena jamaah ini termasuk dalam kategori Mustahik dan Anda tidak memiliki izin untuk melihat data sensitif.
      </div>

      <Card class="p-5">
        <div class="grid gap-4 md:grid-cols-2">
          <div v-for="row in detailRows" :key="row[0]" class="rounded-xl border border-slate-100 bg-slate-50/70 p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ row[0] }}</p>
            <p v-if="row[0] === 'Status'" class="mt-2"><StatusBadge :status="row[1]" /></p>
            <p v-else class="mt-2 text-sm text-slate-900 capitalize">{{ row[1] }}</p>
          </div>
        </div>
      </Card>

      <Card class="p-5">
        <p class="text-sm font-semibold text-slate-900">Alamat</p>
        <p class="mt-2 text-sm text-slate-600">{{ jamaah.address || '-' }}</p>
      </Card>

      <Card class="p-5">
        <p class="text-sm font-semibold text-slate-900">Catatan</p>
        <p class="mt-2 text-sm text-slate-600">{{ jamaah.notes || '-' }}</p>
      </Card>
    </div>
  </AuthenticatedLayout>
</template>
