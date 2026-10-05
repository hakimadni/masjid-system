<script setup>
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
import Dialog from "@/Components/ui/dialog/Dialog.vue"
import MobileFab from "@/Components/MobileFab.vue"
import ConfirmDialog from "@/Components/ui/dialog/ConfirmDialog.vue"
import PageHeader from "@/Components/ui/page/PageHeader.vue"

const props = defineProps({
  filters: { type: Object, required: true },
  prayerSchedules: { type: Object, required: true },
  serviceSchedules: { type: Object, required: true },
  summary: { type: Object, required: true },
})

const page = usePage()
const flash = computed(() => page.props.flash ?? {})
const errors = computed(() => page.props.errors ?? {})
const loadingPrayerTable = ref(false)
const loadingServiceTable = ref(false)
const confirmDelete = ref(false)
const deleteTarget = ref(null)
const deleteForm = useForm({})

const filterForm = useForm({
  search: props.filters.search ?? "",
  status: props.filters.status ?? "",
  start_date: props.filters.start_date ?? "",
  end_date: props.filters.end_date ?? "",
})

const initialTab = (() => {
  if (typeof window === "undefined") return "prayer"
  const params = new URLSearchParams(window.location.search)
  const tab = params.get("tab")
  return tab === "service" || tab === "prayer" ? tab : "prayer"
})()
const activeTab = ref(initialTab)
const prayerSection = ref(null)
const serviceSection = ref(null)

const scrollToTab = (tab) => {
  activeTab.value = tab
  const target = tab === "service" ? serviceSection.value : prayerSection.value
  if (typeof window === "undefined" || !target) return
  window.requestAnimationFrame(() => {
    target.scrollIntoView({ behavior: "smooth", block: "start" })
  })
}

const createDialogOpen = ref(false)
const prayerForm = useForm({
  kind: "prayer",
  schedule_date: new Date().toISOString().slice(0, 10),
  prayer_name: "subuh",
  prayer_time: "",
  imam_name: "",
  muadzin_name: "",
  khatib_name: "",
  status: "draft",
  notes: "",
})

const serviceForm = useForm({
  kind: "service",
  title: "",
  role_type: "imam",
  person_name: "",
  location: "",
  scheduled_at: "",
  status: "draft",
  notes: "",
})

const errorFor = (key) => errors.value[key] ?? ""

const visitTable = (url, target = "all") => {
  if (!url) return
  if (target === "prayer" || target === "all") loadingPrayerTable.value = true
  if (target === "service" || target === "all") loadingServiceTable.value = true

  router.get(url, filterForm.data(), {
    preserveState: true,
    replace: true,
    preserveScroll: true,
    onFinish: () => {
      loadingPrayerTable.value = false
      loadingServiceTable.value = false
    },
  })
}

const applyFilters = () => visitTable(route("schedules.index"))

const resetFilters = () => {
  filterForm.reset()
  applyFilters()
}

const submitPrayer = () => prayerForm.post(route("schedules.store-prayer"), { preserveScroll: true, onSuccess: () => { createDialogOpen.value = false; prayerForm.reset(); } })
const submitService = () => serviceForm.post(route("schedules.store-service"), { preserveScroll: true, onSuccess: () => { createDialogOpen.value = false; serviceForm.reset(); } })

const updatePrayerStatus = (id, status) =>
  useForm({ status }).patch(route("schedules.prayer-status", id), { preserveScroll: true })
const updateServiceStatus = (id, status) =>
  useForm({ status }).patch(route("schedules.service-status", id), { preserveScroll: true })

const openDeleteDialog = (type, item) => {
  deleteTarget.value = { type, item }
  confirmDelete.value = true
}

const closeDeleteDialog = () => {
  confirmDelete.value = false
  deleteTarget.value = null
}

const destroySelected = () => {
  if (!deleteTarget.value) return
  const { type, item } = deleteTarget.value
  const routeName = type === "prayer" ? "schedules.destroy-prayer" : "schedules.destroy-service"
  deleteForm.delete(route(routeName, item.id), {
    preserveScroll: true,
    onSuccess: closeDeleteDialog,
  })
}

const prayerActionItems = (item) => [
  { key: "publish", label: "Publish", disabled: item.status === "published" || item.status === "completed" },
  { key: "complete", label: "Selesai", disabled: item.status === "completed" },
  { key: "archive", label: "Arsipkan", tone: "danger", disabled: item.status === "archived" },
  { key: "delete", label: "Hapus", tone: "danger" },
]

const serviceActionItems = (item) => [
  { key: "publish", label: "Publish", disabled: item.status === "published" || item.status === "completed" },
  { key: "complete", label: "Selesai", disabled: item.status === "completed" },
  { key: "archive", label: "Arsipkan", tone: "danger", disabled: item.status === "archived" },
  { key: "delete", label: "Hapus", tone: "danger" },
]

const handlePrayerAction = (item, key) => {
  if (key === "publish") updatePrayerStatus(item.id, "published")
  if (key === "complete") updatePrayerStatus(item.id, "completed")
  if (key === "archive") updatePrayerStatus(item.id, "archived")
  if (key === "delete") openDeleteDialog("prayer", item)
}

const handleServiceAction = (item, key) => {
  if (key === "publish") updateServiceStatus(item.id, "published")
  if (key === "complete") updateServiceStatus(item.id, "completed")
  if (key === "archive") updateServiceStatus(item.id, "archived")
  if (key === "delete") openDeleteDialog("service", item)
}
</script>

<template>
  <Head title="Jadwal" />

  <AuthenticatedLayout title="Jadwal">
    <div class="space-y-6">
      

      <PageHeader title="Jadwal" description="Kelola jadwal shalat, khatib, dan petugas operasional masjid." />
      <div class="flex gap-2 mb-4">
        <Button
          :class="[
            'px-4 py-2 rounded text-sm font-medium transition-colors',
            activeTab === 'prayer'
              ? 'bg-slate-900 text-white'
              : 'bg-white border border-slate-300 text-slate-900 hover:bg-slate-50'
          ]"
          @click="scrollToTab('prayer')"
        >
          Jadwal Shalat
        </Button>
        <Button
          :class="[
            'px-4 py-2 rounded text-sm font-medium transition-colors',
            activeTab === 'service'
              ? 'bg-slate-900 text-white'
              : 'bg-white border border-slate-300 text-slate-900 hover:bg-slate-50'
          ]"
          @click="scrollToTab('service')"
        >
          Jadwal Petugas
        </Button>
      </div>

      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <StatCard title="Jadwal Mendatang" :value="summary.upcoming_total" />
        <StatCard title="Terpublikasi" :value="summary.published_total" />
        <StatCard title="Selesai" :value="summary.completed_total" />
        <StatCard title="Total Jadwal" :value="summary.records_total" />
      </div>

      <Card class="p-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
          <div>
            <p class="text-sm font-semibold text-slate-900">Filter Jadwal</p>
            <p class="mt-1 text-sm text-slate-500">Gabungkan pencarian jadwal shalat dan petugas operasional.</p>
          </div>
          <div class="flex gap-2">
            <Button variant="outline" @click="resetFilters">Reset</Button>
            <Button :disabled="filterForm.processing" @click="applyFilters">{{ filterForm.processing ? "Memuat..." : "Terapkan" }}</Button>
          </div>
        </div>
        <div class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-4">
          <Input v-model="filterForm.search" placeholder="Cari nama / judul / catatan" />
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

      <div class="grid gap-6 xl:grid-cols-2">
        

        
      </div>

      <div class="grid gap-6 xl:grid-cols-2">
        <Card class="p-0">
          <div class="border-b border-slate-200 px-5 py-4">
            <p class="text-sm font-semibold text-slate-900">Daftar Jadwal Shalat</p>
          </div>
          <div class="p-5">
            <TableSkeleton v-if="loadingPrayerTable" :rows="5" :columns="4" />
            <template v-else>
              <DataTable v-if="prayerSchedules.data.length">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                  <tr>
                    <th class="px-4 py-3 text-left">Shalat</th>
                    <th class="px-4 py-3 text-left">Petugas</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in prayerSchedules.data" :key="item.id" class="border-t border-slate-100">
                    <td class="px-4 py-3">
                      <p class="font-medium text-slate-900">{{ item.prayer_name }}</p>
                      <p class="text-xs text-slate-500">{{ item.schedule_date }} {{ item.prayer_time || "" }}</p>
                    </td>
                    <td class="px-4 py-3 text-sm text-slate-600">
                      <div>Imam: {{ item.imam_name || "-" }}</div>
                      <div>Muadzin: {{ item.muadzin_name || "-" }}</div>
                    </td>
                    <td class="px-4 py-3"><StatusBadge :status="item.status" /></td>
                    <td class="px-4 py-3 text-right">
                      <div class="flex justify-end">
                        <ActionMenu :items="prayerActionItems(item)" @select="handlePrayerAction(item, $event)" />
                      </div>
                    </td>
                  </tr>
                </tbody>
              </DataTable>
              <EmptyState v-else title="Belum ada jadwal shalat" description="Tambahkan jadwal imam, muadzin, atau khatib dari form di atas." />
            </template>

            <div class="mt-4 flex items-center justify-between text-sm text-slate-500">
              <p>Total {{ prayerSchedules.total }} jadwal shalat.</p>
              <div class="flex gap-2">
                <Button size="sm" variant="outline" :disabled="!prayerSchedules.prev_page_url" @click="visitTable(prayerSchedules.prev_page_url, 'prayer')">Sebelumnya</Button>
                <Button size="sm" variant="outline" :disabled="!prayerSchedules.next_page_url" @click="visitTable(prayerSchedules.next_page_url, 'prayer')">Berikutnya</Button>
              </div>
            </div>
          </div>
        </Card>

        <Card class="p-0">
          <div class="border-b border-slate-200 px-5 py-4">
            <p class="text-sm font-semibold text-slate-900">Daftar Jadwal Petugas</p>
          </div>
          <div class="p-5">
            <TableSkeleton v-if="loadingServiceTable" :rows="5" :columns="4" />
            <template v-else>
              <DataTable v-if="serviceSchedules.data.length">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                  <tr>
                    <th class="px-4 py-3 text-left">Agenda</th>
                    <th class="px-4 py-3 text-left">Petugas</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in serviceSchedules.data" :key="item.id" class="border-t border-slate-100">
                    <td class="px-4 py-3">
                      <p class="font-medium text-slate-900">{{ item.title }}</p>
                      <p class="text-xs text-slate-500">{{ item.location || "-" }} • {{ item.scheduled_at }}</p>
                    </td>
                    <td class="px-4 py-3">
                      <p class="text-sm text-slate-700">{{ item.person_name }}</p>
                      <p class="text-xs uppercase text-slate-500">{{ item.role_type }}</p>
                    </td>
                    <td class="px-4 py-3"><StatusBadge :status="item.status" /></td>
                    <td class="px-4 py-3 text-right">
                      <div class="flex justify-end">
                        <ActionMenu :items="serviceActionItems(item)" @select="handleServiceAction(item, $event)" />
                      </div>
                    </td>
                  </tr>
                </tbody>
              </DataTable>
              <EmptyState v-else title="Belum ada jadwal petugas" description="Tambahkan jadwal operasional, imam, atau kegiatan dari panel sebelah." />
            </template>

            <div class="mt-4 flex items-center justify-between text-sm text-slate-500">
              <p>Total {{ serviceSchedules.total }} jadwal petugas.</p>
              <div class="flex gap-2">
                <Button size="sm" variant="outline" :disabled="!serviceSchedules.prev_page_url" @click="visitTable(serviceSchedules.prev_page_url, 'service')">Sebelumnya</Button>
                <Button size="sm" variant="outline" :disabled="!serviceSchedules.next_page_url" @click="visitTable(serviceSchedules.next_page_url, 'service')">Berikutnya</Button>
              </div>
            </div>
          </div>
        </Card>
      </div>

      <ConfirmDialog
        :open="confirmDelete"
        title="Hapus Jadwal"
        :description="deleteTarget ? `Hapus ${deleteTarget.type === 'prayer' ? 'jadwal shalat' : 'jadwal petugas'} ini? Tindakan ini tidak dapat dibatalkan.` : 'Hapus jadwal ini?'"
        confirm-text="Hapus"
        cancel-text="Batal"
        :processing="deleteForm.processing"
        :danger="true"
        @update:open="confirmDelete = $event"
        @cancel="closeDeleteDialog"
        @confirm="destroySelected"
      />
    </div>
  
    <MobileFab @click="createDialogOpen = true" />
    <Dialog :open="createDialogOpen" @close="createDialogOpen = false">
      <div class="p-5 max-h-[85vh] overflow-y-auto">
        <p class="text-sm font-semibold text-slate-900">Jadwal Shalat / Khatib</p>
          <div class="mt-4 grid gap-3 md:grid-cols-2">
            <div>
              <label class="mb-1.5 block text-sm font-medium text-slate-900">Tanggal</label>
              <Input v-model="prayerForm.schedule_date" type="date" />
              <p v-if="errorFor('schedule_date')" class="mt-1 text-xs text-rose-600">{{ errorFor("schedule_date") }}</p>
            </div>
            <div>
              <label class="mb-1.5 block text-sm font-medium text-slate-900">Nama shalat</label>
              <Select v-model="prayerForm.prayer_name">
                <option value="subuh">Subuh</option>
                <option value="dzuhur">Dzuhur</option>
                <option value="ashar">Ashar</option>
                <option value="maghrib">Maghrib</option>
                <option value="isya">Isya</option>
                <option value="jumat">Jumat</option>
              </Select>
              <p v-if="errorFor('prayer_name')" class="mt-1 text-xs text-rose-600">{{ errorFor("prayer_name") }}</p>
            </div>
            <div>
              <label class="mb-1.5 block text-sm font-medium text-slate-900">Waktu</label>
              <Input v-model="prayerForm.prayer_time" type="time" />
              <p v-if="errorFor('prayer_time')" class="mt-1 text-xs text-rose-600">{{ errorFor("prayer_time") }}</p>
            </div>
            <div>
              <label class="mb-1.5 block text-sm font-medium text-slate-900">Status</label>
              <Select v-model="prayerForm.status">
                <option value="draft">Draft</option>
                <option value="published">Published</option>
                <option value="completed">Completed</option>
                <option value="archived">Archived</option>
              </Select>
              <p v-if="errorFor('status')" class="mt-1 text-xs text-rose-600">{{ errorFor("status") }}</p>
            </div>
            <div>
              <label class="mb-1.5 block text-sm font-medium text-slate-900">Nama imam</label>
              <Input v-model="prayerForm.imam_name" placeholder="Nama imam" />
              <p v-if="errorFor('imam_name')" class="mt-1 text-xs text-rose-600">{{ errorFor("imam_name") }}</p>
            </div>
            <div>
              <label class="mb-1.5 block text-sm font-medium text-slate-900">Nama muadzin</label>
              <Input v-model="prayerForm.muadzin_name" placeholder="Nama muadzin" />
              <p v-if="errorFor('muadzin_name')" class="mt-1 text-xs text-rose-600">{{ errorFor("muadzin_name") }}</p>
            </div>
            <div class="md:col-span-2">
              <label class="mb-1.5 block text-sm font-medium text-slate-900">Nama khatib</label>
              <Input v-model="prayerForm.khatib_name" placeholder="Opsional untuk jadwal Jumat" />
              <p v-if="errorFor('khatib_name')" class="mt-1 text-xs text-rose-600">{{ errorFor("khatib_name") }}</p>
            </div>
            <div class="md:col-span-2">
              <label class="mb-1.5 block text-sm font-medium text-slate-900">Catatan</label>
              <Textarea v-model="prayerForm.notes" :rows="3" placeholder="Tambahkan catatan perubahan petugas atau konteks jadwal." />
              <p v-if="errorFor('notes')" class="mt-1 text-xs text-rose-600">{{ errorFor("notes") }}</p>
            </div>
          </div>
          <div class="mt-3">
            <Button class="w-full" :disabled="prayerForm.processing" @click="submitPrayer">{{ prayerForm.processing ? "Menyimpan..." : "Simpan Jadwal Shalat" }}</Button>
          </div><hr class="my-6 border-slate-200" /><p class="text-sm font-semibold text-slate-900">Jadwal Petugas</p>
          <div class="mt-4 grid gap-3 md:grid-cols-2">
            <div>
              <label class="mb-1.5 block text-sm font-medium text-slate-900">Judul tugas / agenda</label>
              <Input v-model="serviceForm.title" placeholder="Contoh: Petugas kajian malam Jumat" />
              <p v-if="errorFor('title')" class="mt-1 text-xs text-rose-600">{{ errorFor("title") }}</p>
            </div>
            <div>
              <label class="mb-1.5 block text-sm font-medium text-slate-900">Jenis petugas</label>
              <Select v-model="serviceForm.role_type">
                <option value="imam">Imam</option>
                <option value="muadzin">Muadzin</option>
                <option value="khatib">Khatib</option>
                <option value="petugas">Petugas</option>
                <option value="kegiatan">Kegiatan</option>
              </Select>
              <p v-if="errorFor('role_type')" class="mt-1 text-xs text-rose-600">{{ errorFor("role_type") }}</p>
            </div>
            <div>
              <label class="mb-1.5 block text-sm font-medium text-slate-900">Nama petugas</label>
              <Input v-model="serviceForm.person_name" placeholder="Nama petugas" />
              <p v-if="errorFor('person_name')" class="mt-1 text-xs text-rose-600">{{ errorFor("person_name") }}</p>
            </div>
            <div>
              <label class="mb-1.5 block text-sm font-medium text-slate-900">Lokasi</label>
              <Input v-model="serviceForm.location" placeholder="Aula utama / serambi" />
              <p v-if="errorFor('location')" class="mt-1 text-xs text-rose-600">{{ errorFor("location") }}</p>
            </div>
            <div>
              <label class="mb-1.5 block text-sm font-medium text-slate-900">Waktu terjadwal</label>
              <Input v-model="serviceForm.scheduled_at" type="datetime-local" />
              <p v-if="errorFor('scheduled_at')" class="mt-1 text-xs text-rose-600">{{ errorFor("scheduled_at") }}</p>
            </div>
            <div>
              <label class="mb-1.5 block text-sm font-medium text-slate-900">Status</label>
              <Select v-model="serviceForm.status">
                <option value="draft">Draft</option>
                <option value="published">Published</option>
                <option value="completed">Completed</option>
                <option value="archived">Archived</option>
              </Select>
              <p v-if="errorFor('status')" class="mt-1 text-xs text-rose-600">{{ errorFor("status") }}</p>
            </div>
            <div class="md:col-span-2">
              <label class="mb-1.5 block text-sm font-medium text-slate-900">Catatan</label>
              <Textarea v-model="serviceForm.notes" :rows="3" placeholder="Tambahkan kebutuhan perlengkapan, PIC, atau pengingat." />
              <p v-if="errorFor('notes')" class="mt-1 text-xs text-rose-600">{{ errorFor("notes") }}</p>
            </div>
          </div>
          <div class="mt-3">
            <Button class="w-full" :disabled="serviceForm.processing" @click="submitService">{{ serviceForm.processing ? "Menyimpan..." : "Simpan Jadwal Petugas" }}</Button>
          </div>
      </div>
    </Dialog>
  </AuthenticatedLayout>

</template>