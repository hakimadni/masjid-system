<script setup>
import { computed, ref } from "vue"
import { Head, router, useForm, usePage, Link } from "@inertiajs/vue3"
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
  events: { type: Object, required: true },
  summary: { type: Object, required: true },
})

const page = usePage()
const flash = computed(() => page.props.flash ?? {})
const errors = computed(() => page.props.errors ?? {})
const loadingTable = ref(false)
const confirmDelete = ref(false)
const selectedEvent = ref(null)
const deleteForm = useForm({})

const filterForm = useForm({
  search: props.filters.search ?? "",
  status: props.filters.status ?? "",
  start_date: props.filters.start_date ?? "",
  end_date: props.filters.end_date ?? "",
})

const createForm = useForm({
  title: "",
  start_at: "",
  end_at: "",
  location: "",
  pic_name: "",
  status: "draft",
  notes: "",
})

const errorFor = (key) => errors.value[key] ?? ""

const visitTable = (url, data = filterForm.data()) => {
  loadingTable.value = true
  router.get(url, data, {
    preserveState: true,
    replace: true,
    preserveScroll: true,
    onFinish: () => {
      loadingTable.value = false
    },
  })
}

const applyFilters = () => visitTable(route("events.index"))

const resetFilters = () => {
  filterForm.reset()
  applyFilters()
}

const submitCreate = () => createForm.post(route("events.store"), { preserveScroll: true })
const updateStatus = (id, status) =>
  useForm({ status }).patch(route("events.update-status", id), { preserveScroll: true })

const openDeleteDialog = (item) => {
  selectedEvent.value = item
  confirmDelete.value = true
}

const closeDeleteDialog = () => {
  confirmDelete.value = false
  selectedEvent.value = null
}

const destroyEvent = () => {
  if (!selectedEvent.value) return
  deleteForm.delete(route("events.destroy", selectedEvent.value.id), {
    preserveScroll: true,
    onSuccess: closeDeleteDialog,
  })
}

const actionItems = (item) => [
  { key: "detail", label: "Detail" },
  { key: "publish", label: "Publish", disabled: item.status === "published" || item.status === "completed" },
  { key: "complete", label: "Selesai", disabled: item.status === "completed" },
  { key: "archive", label: "Arsipkan", tone: "danger", disabled: item.status === "archived" },
  { key: "delete", label: "Hapus", tone: "danger" },
]

const handleAction = (item, key) => {
  if (key === "detail") router.visit(route("events.show", item.id))
  if (key === "publish") updateStatus(item.id, "published")
  if (key === "complete") updateStatus(item.id, "completed")
  if (key === "archive") updateStatus(item.id, "archived")
  if (key === "delete") openDeleteDialog(item)
}
</script>

<template>
  <Head title="Kegiatan" />

  <AuthenticatedLayout title="Kegiatan">
    <div class="space-y-6">
      <div v-if="flash.success" class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
        {{ flash.success }}
      </div>

      <PageHeader title="Kegiatan" description="Kelola agenda masjid, status publikasi, dan PIC kegiatan." />

      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <StatCard title="Agenda Mendatang" :value="summary.upcoming_total" />
        <StatCard title="Sudah Publish" :value="summary.published_total" />
        <StatCard title="Selesai" :value="summary.completed_total" />
        <StatCard title="Total Agenda" :value="summary.records_total" />
      </div>

      <Card class="p-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
          <div>
            <p class="text-sm font-semibold text-slate-900">Filter Kegiatan</p>
            <p class="mt-1 text-sm text-slate-500">Pantau agenda masjid berdasarkan status, tanggal, PIC, dan lokasi.</p>
          </div>
          <div class="flex gap-2">
            <Button variant="outline" @click="resetFilters">Reset</Button>
            <Button :disabled="filterForm.processing" @click="applyFilters">{{ filterForm.processing ? "Memuat..." : "Terapkan" }}</Button>
          </div>
        </div>

        <div class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-4">
          <Input v-model="filterForm.search" placeholder="Cari judul / lokasi / PIC" />
          <Select v-model="filterForm.status">
            <option value="">Semua status</option>
            <option value="draft">Draft</option>
            <option value="published">Published</option>
            <option value="completed">Completed</option>
            <option value="archived">Archived</option>
          </Select>
          <Input v-model="filterForm.start_date" type="date" />
          <Input v-model="filterForm.end_date" type="date" />
        </div>
      </Card>

      <Card class="p-5">
        <div>
          <p class="text-sm font-semibold text-slate-900">Buat Agenda Baru</p>
          <p class="mt-1 text-sm text-slate-500">Catat kajian, program sosial, dan agenda operasional dengan PIC yang jelas.</p>
        </div>

        <div class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-4">
          <div class="xl:col-span-2">
            <label class="mb-1.5 block text-sm font-medium text-slate-900">Judul kegiatan</label>
            <Input v-model="createForm.title" placeholder="Kajian Ahad Pagi" />
            <p v-if="errorFor('title')" class="mt-1 text-xs text-rose-600">{{ errorFor("title") }}</p>
          </div>
          <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-900">Mulai</label>
            <Input v-model="createForm.start_at" type="datetime-local" />
            <p v-if="errorFor('start_at')" class="mt-1 text-xs text-rose-600">{{ errorFor("start_at") }}</p>
          </div>
          <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-900">Selesai</label>
            <Input v-model="createForm.end_at" type="datetime-local" />
            <p v-if="errorFor('end_at')" class="mt-1 text-xs text-rose-600">{{ errorFor("end_at") }}</p>
          </div>
          <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-900">Lokasi</label>
            <Input v-model="createForm.location" placeholder="Aula Utama" />
            <p v-if="errorFor('location')" class="mt-1 text-xs text-rose-600">{{ errorFor("location") }}</p>
          </div>
          <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-900">PIC</label>
            <Input v-model="createForm.pic_name" placeholder="Sekretaris DKM" />
            <p v-if="errorFor('pic_name')" class="mt-1 text-xs text-rose-600">{{ errorFor("pic_name") }}</p>
          </div>
          <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-900">Status</label>
            <Select v-model="createForm.status">
              <option value="draft">Draft</option>
              <option value="published">Published</option>
              <option value="completed">Completed</option>
            </Select>
            <p v-if="errorFor('status')" class="mt-1 text-xs text-rose-600">{{ errorFor("status") }}</p>
          </div>
        </div>

        <div class="mt-3 grid gap-3 md:grid-cols-[1fr_auto]">
          <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-900">Catatan</label>
            <Textarea v-model="createForm.notes" :rows="3" placeholder="Agenda, kebutuhan teknis, atau konteks koordinasi." />
            <p v-if="errorFor('notes')" class="mt-1 text-xs text-rose-600">{{ errorFor("notes") }}</p>
          </div>
          <div class="flex items-end">
            <Button :disabled="createForm.processing" @click="submitCreate">{{ createForm.processing ? "Menyimpan..." : "Simpan Kegiatan" }}</Button>
          </div>
        </div>
      </Card>

      <Card class="p-0">
        <div class="border-b border-slate-200 px-5 py-4">
          <p class="text-sm font-semibold text-slate-900">Daftar Kegiatan</p>
        </div>

        <div class="p-5">
          <TableSkeleton v-if="loadingTable" :rows="6" :columns="5" />

          <template v-else>
            <DataTable v-if="events.data.length">
              <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                  <th class="px-4 py-3 text-left">Agenda</th>
                  <th class="px-4 py-3 text-left">Waktu</th>
                  <th class="px-4 py-3 text-left">Lokasi</th>
                  <th class="px-4 py-3 text-left">Status</th>
                  <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in events.data" :key="item.id" class="border-t border-slate-100">
                  <td class="px-4 py-3">
                    <p class="font-medium text-slate-900">{{ item.title }}</p>
                    <p class="text-xs text-slate-500">{{ item.pic_name || "-" }}</p>
                  </td>
                  <td class="px-4 py-3 text-sm text-slate-600">
                    <p>{{ item.start_at }}</p>
                    <p class="text-xs text-slate-500">{{ item.end_at || "Sampai selesai" }}</p>
                  </td>
                  <td class="px-4 py-3 text-sm text-slate-600">
                    <p>{{ item.location || "-" }}</p>
                    <p class="text-xs text-slate-500">{{ item.notes || "Tanpa catatan" }}</p>
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

            <EmptyState
              v-else
              title="Belum ada kegiatan"
              description="Agenda kajian dan program sosial akan tampil di sini setelah ditambahkan."
            />
          </template>
        </div>
      </Card>

      <ConfirmDialog
        :open="confirmDelete"
        title="Hapus Kegiatan"
        :description="selectedEvent ? `Hapus kegiatan ${selectedEvent.title}? Tindakan ini tidak dapat dibatalkan.` : 'Hapus kegiatan ini?'"
        confirm-text="Hapus"
        cancel-text="Batal"
        :processing="deleteForm.processing"
        :danger="true"
        @update:open="confirmDelete = $event"
        @cancel="closeDeleteDialog"
        @confirm="destroyEvent"
      />
    </div>
  </AuthenticatedLayout>
</template>