<script setup>
import { computed, ref } from "vue"
import { Head, router, useForm, usePage, Link } from "@inertiajs/vue3"
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import StatCard from "@/Components/qurban/StatCard.vue"
import Card from "@/Components/ui/card/Card.vue"
import Button from "@/Components/ui/button/Button.vue"
import DataTable from "@/Components/ui/table/DataTable.vue"
import EmptyState from "@/Components/ui/empty/EmptyState.vue"
import Input from "@/Components/ui/input/Input.vue"
import Select from "@/Components/ui/select/Select.vue"
import StatusBadge from "@/Components/ui/status/StatusBadge.vue"
import PageHeader from "@/Components/ui/page/PageHeader.vue"
import ConfirmDialog from "@/Components/ui/dialog/ConfirmDialog.vue"

const props = defineProps({
  media: { type: Object, required: true },
  filters: { type: Object, required: true },
  types: { type: Array, required: true },
})

const page = usePage()
const flash = computed(() => page.props.flash ?? {})
const errors = computed(() => page.props.errors ?? {})
const deleteDialog = ref({
  open: false,
  itemId: null,
})

const filterForm = useForm({
  search: props.filters.search ?? "",
  type: props.filters.type ?? "",
  status: props.filters.status ?? "",
})

const applyFilters = () =>
  filterForm.get(route("media.index"), {
    preserveState: true,
    replace: true,
    preserveScroll: true,
  })

const resetFilters = () => {
  filterForm.reset()
  applyFilters()
}

const openPage = (url) => {
  if (!url) return
  router.visit(url, { preserveScroll: true, preserveState: true })
}

const openDeleteDialog = (id) => {
  deleteDialog.value.itemId = id
  deleteDialog.value.open = true
}

const confirmDelete = () => {
  if (!deleteDialog.value.itemId) return

  router.delete(route('media.destroy', deleteDialog.value.itemId), {
    preserveScroll: true,
    onFinish: () => {
      deleteDialog.value.open = false
      deleteDialog.value.itemId = null
    },
  })
}
</script>

<template>
  <Head title="Media" />

  <AuthenticatedLayout title="Media">
    <div class="space-y-6">
      <PageHeader title="Media" description="Kelola video, audio, gambar, dan dokumen media untuk portal masjid." />

      <div v-if="flash.success" class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
        {{ flash.success }}
      </div>

      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <StatCard title="Total Media" :value="media.total" />
        <StatCard title="Aktif" :value="media.data.filter(m => m.is_active).length" />
        <StatCard title="YouTube" :value="media.data.filter(m => m.type === 'youtube').length" />
        <StatCard title="Lainnya" :value="media.data.filter(m => m.type !== 'youtube').length" />
      </div>

      <Card class="p-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
          <div>
            <p class="text-sm font-semibold text-slate-900">Filter Media</p>
            <p class="mt-1 text-sm text-slate-500">Filter berdasarkan judul, tipe, dan status.</p>
          </div>
          <div class="flex gap-2">
            <Button variant="outline" @click="resetFilters">Reset</Button>
            <Button @click="applyFilters">Terapkan</Button>
          </div>
        </div>
        <div class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-4">
          <Input v-model="filterForm.search" placeholder="Cari judul / deskripsi" />
          <Select v-model="filterForm.type">
            <option value="">Semua tipe</option>
            <option v-for="type in types" :key="type.value" :value="type.value">
              {{ type.label }}
            </option>
          </Select>
          <Select v-model="filterForm.status">
            <option value="">Semua status</option>
            <option value="active">Aktif</option>
            <option value="inactive">Tidak Aktif</option>
          </Select>
        </div>
      </Card>

      <Card class="p-0">
        <div class="border-b border-slate-200 px-5 py-4">
          <p class="text-sm font-semibold text-slate-900">Daftar Media</p>
        </div>
        <div class="p-5">
          <DataTable v-if="media.data.length">
            <thead class="bg-slate-50 text-xs uppercase text-slate-500">
              <tr>
                <th class="px-4 py-3 text-left">Judul</th>
                <th class="px-4 py-3 text-left">Tipe</th>
                <th class="px-4 py-3 text-left">URL</th>
                <th class="px-4 py-3 text-left">Status</th>
                <th class="px-4 py-3 text-left">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in media.data" :key="item.id" class="border-t border-slate-100">
                <td class="px-4 py-3">
                  <p class="font-medium text-slate-900">{{ item.title }}</p>
                  <p v-if="item.description" class="text-xs text-slate-500">{{ item.description }}</p>
                </td>
                <td class="px-4 py-3 text-sm text-slate-600">
                  <span v-if="item.type === 'youtube'" class="bg-indigo-100 text-indigo-800 text-xs px-2 py-1 rounded">YouTube</span>
                  <span v-else-if="item.type === 'video'" class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">Video</span>
                  <span v-else-if="item.type === 'audio'" class="bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded">Audio</span>
                  <span v-else-if="item.type === 'image'" class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">Gambar</span>
                  <span v-else class="bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded">Dokumen</span>
                </td>
                <td class="px-4 py-3 break-all text-xs">
                  <a :href="item.url" target="_blank" rel="noopener noreferrer" class="text-indigo-600 hover:underline">
                    {{ item.url.length > 50 ? item.url.substring(0, 50) + '...' : item.url }}
                  </a>
                </td>
                <td class="px-4 py-3"><StatusBadge :status="item.is_active ? 'published' : 'draft'" /></td>
                <td class="space-y-2 px-4 py-3">
                  <Link :href="route('media.edit', item.id)"><Button size="sm" class="w-full">Edit</Button></Link>
                  <Button size="sm" variant="outline" class="w-full" @click="openDeleteDialog(item.id)">Hapus</Button>
                </td>
              </tr>
            </tbody>
          </DataTable>
          <EmptyState v-else title="Belum ada media" description="Tambahkan media seperti video YouTube, audio pengajian, atau gambar dari form di atas." />
          
          <div class="mt-4 flex items-center justify-between text-sm text-slate-500">
            <p>Total {{ media.total }} media.</p>
            <div class="flex gap-2">
              <Button size="sm" variant="outline" :disabled="!media.prev_page_url" @click="openPage(media.prev_page_url)">Sebelumnya</Button>
              <Button size="sm" variant="outline" :disabled="!media.next_page_url" @click="openPage(media.next_page_url)">Berikutnya</Button>
            </div>
          </div>
        </div>
      </Card>
    </div>

    <ConfirmDialog
      v-model:open="deleteDialog.open"
      title="Hapus Media"
      description="Media yang dihapus tidak dapat dipulihkan. Pastikan item yang dipilih sudah benar."
      confirm-text="Ya, Hapus"
      cancel-text="Batal"
      :danger="true"
      @confirm="confirmDelete"
    />
  </AuthenticatedLayout>
</template>
