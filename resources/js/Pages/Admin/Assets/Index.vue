<script setup>
import Dialog from "@/Components/ui/dialog/Dialog.vue"
import MobileFab from "@/Components/MobileFab.vue"
import { computed, ref } from "vue"
import { Head, router, useForm, usePage } from "@inertiajs/vue3"
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import StatCard from "@/Components/qurban/StatCard.vue"
import Card from "@/Components/ui/card/Card.vue"
import Button from "@/Components/ui/button/Button.vue"
import DataTable from "@/Components/ui/table/DataTable.vue"
import TableSkeleton from "@/Components/ui/table/TableSkeleton.vue"
import EmptyState from "@/Components/ui/empty/EmptyState.vue"
import Input from "@/Components/ui/input/Input.vue"
import Select from "@/Components/ui/select/Select.vue"
import Textarea from "@/Components/ui/textarea/Textarea.vue"
import StatusBadge from "@/Components/ui/status/StatusBadge.vue"
import ActionMenu from "@/Components/ui/dropdown/ActionMenu.vue"
import ConfirmDialog from "@/Components/ui/dialog/ConfirmDialog.vue"
import PageHeader from "@/Components/ui/page/PageHeader.vue"

const props = defineProps({
  filters: { type: Object, required: true },
  assets: { type: Object, required: true },
  summary: { type: Object, required: true },
  categories: { type: Array, required: true },
})

const page = usePage()
const flash = computed(() => page.props.flash ?? {})
const errors = computed(() => page.props.errors ?? {})
const loadingTable = ref(false)

const filterForm = useForm({
  search: props.filters.search ?? "",
  status: props.filters.status ?? "",
  condition: props.filters.condition ?? "",
  category: props.filters.category ?? "",
})

const createDialogOpen = ref(false)
const createForm = useForm({
  name: "",
  category: "",
  location: "",
  quantity: 1,
  condition: "baik",
  status: "active",
  notes: "",
})

const confirmDelete = ref(false)
const selectedItem = ref(null)
const deleteForm = useForm({})

const errorFor = (key) => errors.value[key] ?? ""

const visitTable = (url, data = filterForm.data()) => {
  loadingTable.value = true
  router.get(url, data, {
    preserveState: true,
    replace: true,
    preserveScroll: true,
    onFinish: () => { loadingTable.value = false },
  })
}

const applyFilters = () => visitTable(route("assets.index"))
const resetFilters = () => { filterForm.reset(); applyFilters() }
const submitCreate = () => createForm.post(route("assets.store"), { preserveScroll: true })
const updateStatus = (id, status) => useForm({ status }).patch(route("assets.update-status", id), { preserveScroll: true })

const openDeleteDialog = (item) => { selectedItem.value = item; confirmDelete.value = true }
const closeDeleteDialog = () => { confirmDelete.value = false; selectedItem.value = null }

const destroyAsset = () => {
  if (!selectedItem.value) return
  deleteForm.delete(route("assets.destroy", selectedItem.value.id), {
    preserveScroll: true,
    onSuccess: closeDeleteDialog,
  })
}

const actionItems = (item) => [
  { key: "detail", label: "Detail" },
  { key: "active", label: "Aktifkan", disabled: item.status === "active" },
  { key: "maintenance", label: "Perawatan", disabled: item.status === "maintenance" },
  { key: "archived", label: "Arsipkan", tone: "danger", disabled: item.status === "archived" },
  { key: "delete", label: "Hapus", tone: "danger" },
]

const handleAction = (item, key) => {
  if (key === "detail") router.visit(route("assets.show", item.id))
  if (key === "active") updateStatus(item.id, "active")
  if (key === "maintenance") updateStatus(item.id, "maintenance")
  if (key === "archived") updateStatus(item.id, "archived")
  if (key === "delete") openDeleteDialog(item)
}
</script>

<template>
  <Head title="Inventaris" />

  <AuthenticatedLayout title="Inventaris">
    <div class="space-y-6">
      <div v-if="flash.success" class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
        {{ flash.success }}
      </div>

      <PageHeader title="Inventaris" description="Kelola aset, perlengkapan, dan inventaris masjid." />

      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <StatCard title="Aset Aktif" :value="summary.active_total" />
        <StatCard title="Perlu Perawatan" :value="summary.maintenance_total" />
        <StatCard title="Diarsipkan" :value="summary.archived_total" />
        <StatCard title="Total Unit" :value="summary.quantity_total" />
      </div>

      <Card class="p-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
          <div>
            <p class="text-sm font-semibold text-slate-900">Filter Inventaris</p>
            <p class="mt-1 text-sm text-slate-500">Telusuri barang berdasarkan kategori, kondisi, dan status perawatan.</p>
          </div>
          <div class="flex gap-2">
            <Button variant="outline" @click="resetFilters">Reset</Button>
            <Button :disabled="filterForm.processing" @click="applyFilters">{{ filterForm.processing ? "Memuat..." : "Terapkan" }}</Button>
          </div>
        </div>
        <div class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-4">
          <Input v-model="filterForm.search" placeholder="Cari nama / lokasi / catatan" />
          <Select v-model="filterForm.category">
            <option value="">Semua kategori</option>
            <option v-for="item in categories" :key="item" :value="item">{{ item }}</option>
          </Select>
          <Select v-model="filterForm.condition">
            <option value="">Semua kondisi</option>
            <option value="baik">Baik</option>
            <option value="perlu_perbaikan">Perlu Perbaikan</option>
            <option value="rusak">Rusak</option>
          </Select>
          <Select v-model="filterForm.status">
            <option value="">Semua status</option>
            <option value="active">Aktif</option>
            <option value="maintenance">Perawatan</option>
            <option value="archived">Arsip</option>
          </Select>
        </div>
      </Card>

      

      <Card class="p-0">
        <div class="border-b border-slate-200 px-5 py-4">
          <p class="text-sm font-semibold text-slate-900">Daftar Inventaris</p>
        </div>
        <div class="p-5">
          <TableSkeleton v-if="loadingTable" :rows="6" :columns="5" />
          <template v-else>
            <DataTable v-if="assets.data.length">
              <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                  <th class="px-4 py-3 text-left">Barang</th>
                  <th class="px-4 py-3 text-left">Kategori / Lokasi</th>
                  <th class="px-4 py-3 text-left">Jumlah</th>
                  <th class="px-4 py-3 text-left">Status</th>
                  <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in assets.data" :key="item.id" class="border-t border-slate-100">
                  <td class="px-4 py-3">
                    <p class="font-medium text-slate-900">{{ item.name }}</p>
                    <p class="text-xs text-slate-500">{{ item.notes || "Tanpa catatan" }}</p>
                  </td>
                  <td class="px-4 py-3 text-sm text-slate-600">
                    <p>{{ item.category || "-" }}</p>
                    <p class="text-xs text-slate-500">{{ item.location || "-" }}</p>
                  </td>
                  <td class="px-4 py-3 text-sm text-slate-600">
                    <p>{{ item.quantity }} unit</p>
                    <p class="text-xs text-slate-500">{{ item.condition }}</p>
                  </td>
                  <td class="px-4 py-3"><StatusBadge :status="item.status" /></td>
                  <td class="px-4 py-3 text-right">
                    <div class="flex justify-end">
                      <ActionMenu :items="actionItems(item)" @select="handleAction(item, $event)" />
                    </div>
                  </td>
                </tr>
              </tbody>
            </DataTable>
            <EmptyState v-else title="Belum ada inventaris" description="Perlengkapan dan aset operasional masjid akan muncul di sini." />
          </template>
        </div>
      </Card>

      <ConfirmDialog
        :open="confirmDelete"
        title="Hapus Inventaris"
        :description="selectedItem ? `Hapus ${selectedItem.name} dari daftar inventaris? Tindakan ini tidak dapat dibatalkan.` : 'Hapus aset ini?'"
        confirm-text="Hapus"
        cancel-text="Batal"
        :processing="deleteForm.processing"
        :danger="true"
        @update:open="confirmDelete = $event"
        @cancel="closeDeleteDialog"
        @confirm="destroyAsset"
      />
    </div>
  
    <MobileFab @click="createDialogOpen = true" />
    <Dialog :open="createDialogOpen" @close="createDialogOpen = false">
      <div class="p-5 max-h-[85vh] overflow-y-auto">
        <div>
          <p class="text-sm font-semibold text-slate-900">Tambah Inventaris</p>
          <p class="mt-1 text-sm text-slate-500">Simpan aset utama masjid agar marbot dan pengurus punya sumber data yang sama.</p>
        </div>
        <div class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-4">
          <div class="xl:col-span-2">
            <label class="mb-1.5 block text-sm font-medium text-slate-900">Nama barang</label>
            <Input v-model="createForm.name" placeholder="Karpet utama" />
            <p v-if="errorFor('name')" class="mt-1 text-xs text-rose-600">{{ errorFor("name") }}</p>
          </div>
          <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-900">Kategori</label>
            <Input v-model="createForm.category" placeholder="Perlengkapan ibadah" />
            <p v-if="errorFor('category')" class="mt-1 text-xs text-rose-600">{{ errorFor("category") }}</p>
          </div>
          <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-900">Lokasi</label>
            <Input v-model="createForm.location" placeholder="Ruang shalat utama" />
            <p v-if="errorFor('location')" class="mt-1 text-xs text-rose-600">{{ errorFor("location") }}</p>
          </div>
          <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-900">Jumlah</label>
            <Input v-model="createForm.quantity" type="number" min="1" />
            <p v-if="errorFor('quantity')" class="mt-1 text-xs text-rose-600">{{ errorFor("quantity") }}</p>
          </div>
          <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-900">Kondisi</label>
            <Select v-model="createForm.condition">
              <option value="baik">Baik</option>
              <option value="perlu_perbaikan">Perlu Perbaikan</option>
              <option value="rusak">Rusak</option>
            </Select>
            <p v-if="errorFor('condition')" class="mt-1 text-xs text-rose-600">{{ errorFor("condition") }}</p>
          </div>
          <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-900">Status</label>
            <Select v-model="createForm.status">
              <option value="active">Aktif</option>
              <option value="maintenance">Perawatan</option>
              <option value="archived">Arsip</option>
            </Select>
            <p v-if="errorFor('status')" class="mt-1 text-xs text-rose-600">{{ errorFor("status") }}</p>
          </div>
        </div>
        <div class="mt-3 grid gap-3 md:grid-cols-[1fr_auto]">
          <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-900">Catatan</label>
            <Textarea v-model="createForm.notes" :rows="3" placeholder="Jadwal perawatan, vendor, atau catatan kondisi." />
            <p v-if="errorFor('notes')" class="mt-1 text-xs text-rose-600">{{ errorFor("notes") }}</p>
          </div>
          <div class="flex items-end">
            <Button :disabled="createForm.processing" @click="submitCreate">{{ createForm.processing ? "Menyimpan..." : "Simpan Inventaris" }}</Button>
          </div>
        </div>
      </div>
    </Dialog>
  </AuthenticatedLayout>

</template>