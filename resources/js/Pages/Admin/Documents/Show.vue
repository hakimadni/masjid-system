<script setup>
import { Head, Link } from "@inertiajs/vue3"
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import Card from "@/Components/ui/card/Card.vue"
import StatusBadge from "@/Components/ui/status/StatusBadge.vue"
import Button from "@/Components/ui/button/Button.vue"

const props = defineProps({
  document: { type: Object, required: true },
})

const formatFileSize = (bytes) => {
  if (!bytes) return "-"
  const units = ["B", "KB", "MB", "GB"]
  let i = 0
  let size = bytes
  while (size >= 1024 && i < units.length - 1) { size /= 1024; i++ }
  return `${size.toFixed(1)} ${units[i]}`
}
</script>

<template>
  <Head title="Detail Dokumen" />

  <AuthenticatedLayout title="Detail Dokumen">
    <div class="space-y-6">
      <Card class="p-5">
        <div class="flex items-start justify-between">
          <div>
            <h2 class="text-lg font-semibold text-slate-900">{{ document.title }}</h2>
            <p class="mt-1 text-sm text-slate-500">{{ document.category }}</p>
          </div>
          <StatusBadge :status="document.status" />
        </div>

        <div class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-4">
          <div>
            <p class="text-xs font-medium text-slate-500 uppercase">Nomor Dokumen</p>
            <p class="text-sm text-slate-700">{{ document.document_number || '-' }}</p>
          </div>
          <div>
            <p class="text-xs font-medium text-slate-500 uppercase">Tanggal</p>
            <p class="text-sm text-slate-700">{{ document.document_date || '-' }}</p>
          </div>
          <div>
            <p class="text-xs font-medium text-slate-500 uppercase">Ukuran File</p>
            <p class="text-sm text-slate-700">{{ formatFileSize(document.file_size) }}</p>
          </div>
          <div>
            <p class="text-xs font-medium text-slate-500 uppercase">Diupload oleh</p>
            <p class="text-sm text-slate-700">{{ document.creator?.name || '-' }}</p>
          </div>
        </div>

        <div v-if="document.description" class="mt-4">
          <p class="text-xs font-medium text-slate-500 uppercase">Deskripsi</p>
          <p class="text-sm text-slate-700">{{ document.description }}</p>
        </div>

        <div class="mt-5 flex gap-2">
          <Link v-if="document.file_path" :href="route('documents.download', document.id)" target="_blank">
            <Button size="sm">Download File</Button>
          </Link>
          <Link :href="route('documents.index')">
            <Button variant="outline" size="sm">Kembali</Button>
          </Link>
        </div>
      </Card>
    </div>
  </AuthenticatedLayout>
</template>