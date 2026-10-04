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

const props = defineProps({
  filters: { type: Object, required: true },
  announcements: { type: Object, required: true },
  summary: { type: Object, required: true },
})

const page = usePage()
const flash = computed(() => page.props.flash ?? {})
const errors = computed(() => page.props.errors ?? {})
const loadingTable = ref(false)

const filterForm = useForm({
  search: props.filters.search ?? "",
  status: props.filters.status ?? "",
})

const createDialogOpen = ref(false)
const createForm = useForm({
  title: "",
  content: "",
  status: "draft",
  published_at: "",
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

const applyFilters = () => visitTable(route("announcements.index"))

const resetFilters = () => {
  filterForm.reset()
  applyFilters()
}

const submitCreate = () => createForm.post(route("announcements.store"), { preserveScroll: true })
const updateStatus = (id, status) =>
  useForm({ status }).patch(route("announcements.update-status", id), { preserveScroll: true })

const actionItems = (item) => [
  { key: "publish", label: "Publish", disabled: item.status === "published" },
  { key: "draft", label: "Jadikan Draft", disabled: item.status === "draft" },
  { key: "archive", label: "Arsipkan", tone: "danger", disabled: item.status === "archived" },
]

const handleAction = (item, key) => {
  if (key === "publish") updateStatus(item.id, "published")
  if (key === "draft") updateStatus(item.id, "draft")
  if (key === "archive") updateStatus(item.id, "archived")
}
</script>

<template>
  <Head title="Pengumuman" />

  <AuthenticatedLayout title="Pengumuman">
    <div class="space-y-6">
      

      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <StatCard title="Terpublikasi" :value="summary.published_total" />
        <StatCard title="Draft" :value="summary.draft_total" />
        <StatCard title="Arsip" :value="summary.archived_total" />
        <StatCard title="Total Pengumuman" :value="summary.records_total" />
      </div>

      <Card class="p-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
          <div>
            <p class="text-sm font-semibold text-slate-900">Filter Pengumuman</p>
            <p class="mt-1 text-sm text-slate-500">Cari judul dan isi pengumuman aktif maupun arsip.</p>
          </div>
          <div class="flex gap-2">
            <Button variant="outline" @click="resetFilters">Reset</Button>
            <Button :disabled="filterForm.processing" @click="applyFilters">{{ filterForm.processing ? "Memuat..." : "Terapkan" }}</Button>
          </div>
        </div>

        <div class="mt-4 grid gap-3 md:grid-cols-2">
          <Input v-model="filterForm.search" placeholder="Cari judul / isi pengumuman" />
          <Select v-model="filterForm.status">
            <option value="">Semua status</option>
            <option value="draft">Draft</option>
            <option value="published">Published</option>
            <option value="archived">Archived</option>
          </Select>
        </div>
      </Card>

      

      <Card class="p-0">
        <div class="border-b border-slate-200 px-5 py-4">
          <p class="text-sm font-semibold text-slate-900">Daftar Pengumuman</p>
        </div>

        <div class="p-5">
          <TableSkeleton v-if="loadingTable" :rows="6" :columns="4" />

          <template v-else>
            <DataTable v-if="announcements.data.length">
              <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                  <th class="px-4 py-3 text-left">Judul</th>
                  <th class="px-4 py-3 text-left">Isi Ringkas</th>
                  <th class="px-4 py-3 text-left">Status</th>
                  <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in announcements.data" :key="item.id" class="border-t border-slate-100">
                  <td class="px-4 py-3">
                    <p class="font-medium text-slate-900">{{ item.title }}</p>
                    <p class="text-xs text-slate-500">{{ item.published_at || item.created_at }}</p>
                  </td>
                  <td class="px-4 py-3 text-sm text-slate-600">{{ item.excerpt }}</td>
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
              title="Belum ada pengumuman"
              description="Pengumuman aktif dan arsip akan ditampilkan di sini."
            />
          </template>
        </div>
      </Card>
    </div>
  
    <MobileFab @click="createDialogOpen = true" />
    <Dialog :open="createDialogOpen" @close="createDialogOpen = false">
      <div class="p-5 max-h-[85vh] overflow-y-auto">
        <div>
          <p class="text-sm font-semibold text-slate-900">Buat Pengumuman Baru</p>
          <p class="mt-1 text-sm text-slate-500">Gunakan draft untuk review internal, lalu publish saat siap ditampilkan ke admin dashboard.</p>
        </div>

        <div class="mt-4 grid gap-3 md:grid-cols-2">
          <div class="md:col-span-2">
            <label class="mb-1.5 block text-sm font-medium text-slate-900">Judul pengumuman</label>
            <Input v-model="createForm.title" placeholder="Perubahan jadwal kajian malam Jumat" />
            <p v-if="errorFor('title')" class="mt-1 text-xs text-rose-600">{{ errorFor("title") }}</p>
          </div>
          <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-900">Status</label>
            <Select v-model="createForm.status">
              <option value="draft">Draft</option>
              <option value="published">Published</option>
              <option value="archived">Archived</option>
            </Select>
            <p v-if="errorFor('status')" class="mt-1 text-xs text-rose-600">{{ errorFor("status") }}</p>
          </div>
          <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-900">Waktu publish</label>
            <Input v-model="createForm.published_at" type="datetime-local" />
            <p v-if="errorFor('published_at')" class="mt-1 text-xs text-rose-600">{{ errorFor("published_at") }}</p>
          </div>
          <div class="md:col-span-2">
            <label class="mb-1.5 block text-sm font-medium text-slate-900">Isi pengumuman</label>
            <Textarea v-model="createForm.content" :rows="5" placeholder="Tuliskan informasi yang perlu diketahui pengurus atau jamaah." />
            <p v-if="errorFor('content')" class="mt-1 text-xs text-rose-600">{{ errorFor("content") }}</p>
          </div>
        </div>

        <div class="mt-3 flex justify-end">
          <Button :disabled="createForm.processing" @click="submitCreate">{{ createForm.processing ? "Menyimpan..." : "Simpan Pengumuman" }}</Button>
        </div>
      </div>
    </Dialog>
  </AuthenticatedLayout>

</template>
