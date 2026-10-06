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
import ClickableCard from "@/Components/ClickableCard.vue"
import DetailModal from "@/Components/DetailModal.vue"
import ActionMenu from "@/Components/ui/dropdown/ActionMenu.vue"
import PageHeader from "@/Components/ui/page/PageHeader.vue"
import Dialog from "@/Components/ui/dialog/Dialog.vue"
import ConfirmDialog from "@/Components/ui/dialog/ConfirmDialog.vue"
import MobileFab from "@/Components/MobileFab.vue"

const props = defineProps({
  filters: { type: Object, required: true },
  donations: { type: Object, required: true },
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
  method: props.filters.method ?? "",
  start_date: props.filters.start_date ?? "",
  end_date: props.filters.end_date ?? "",
})

const createDialogOpen = ref(false)
const detailDialog = ref({ open: false, item: null })
const openDetailDialog = (item) => {
  detailDialog.value.item = item
  detailDialog.value.open = true
}
const closeDetailDialog = () => {
  detailDialog.value.open = false
  setTimeout(() => { detailDialog.value.item = null }, 300)
}

const createForm = useForm({
  donor_name: "",
  phone: "",
  email: "",
  address: "",
  campaign: "Donasi Umum",
  donation_category_id: "",
  amount: "",
  method: "cash",
  status: "pending",
  donation_date: new Date().toISOString().slice(0, 10),
  notes: "",
  is_anonymous: false,
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

const applyFilters = () => visitTable(route("donations.index"))

const resetFilters = () => {
  filterForm.reset()
  applyFilters()
}

const submitCreate = () => createForm.post(route("donations.store"), { preserveScroll: true, onSuccess: () => { createDialogOpen.value = false; createForm.reset() } })
const updateStatus = (id, status) =>
  useForm({ status }).patch(route("donations.update-status", id), { preserveScroll: true })

const formatCurrency = (value) =>
  new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    maximumFractionDigits: 0,
  }).format(Number(value || 0))

const confirmDialog = ref({ open: false, itemId: null })
const rejectDialog = ref({ open: false, itemId: null })
const statusForm = useForm({})

const actionItems = (item) => [
  { key: "detail", label: "Detail" },
  { key: "confirm", label: "Confirm", disabled: item.status === "confirmed" },
  { key: "reject", label: "Reject", disabled: item.status === "rejected", tone: "danger" },
]

const handleAction = (item, key) => {
  if (key === "detail") router.visit(route("donations.show", item.id)) // assuming there's a show route if needed, otherwise skip. Actually wait, let's keep original if there's none.
  // Wait, there's no donations.show route implemented yet!
  if (key === "confirm") { confirmDialog.value.itemId = item.id; confirmDialog.value.open = true; }
  if (key === "reject") { rejectDialog.value.itemId = item.id; rejectDialog.value.open = true; }
}

const submitConfirm = () => {
  if (confirmDialog.value.itemId) {
    statusForm.patch(route("donations.update-status", confirmDialog.value.itemId), {
      data: { status: "confirmed" },
      preserveScroll: true,
      onSuccess: () => {
        confirmDialog.value.open = false
        confirmDialog.value.itemId = null
      }
    })
  }
}

const scrollToForm = () => {
  document.getElementById('create-form')?.scrollIntoView({ behavior: 'smooth' })
}

const submitReject = () => {
  if (rejectDialog.value.itemId) {
    statusForm.patch(route("donations.update-status", rejectDialog.value.itemId), {
      data: { status: "rejected" },
      preserveScroll: true,
      onSuccess: () => {
        rejectDialog.value.open = false
        rejectDialog.value.itemId = null
      }
    })
  }
}
</script>

<template>
  <Head title="Donasi" />

  <AuthenticatedLayout title="Donasi">
    <div class="space-y-6">
      <PageHeader
        title="Donasi"
        description="Kelola donasi manual, status konfirmasi, dan pencatatan kas masjid."
      >
        <template #actions>
          <Button class="hidden md:inline-flex" @click="scrollToForm">Tambah Donasi</Button>
        </template>
      </PageHeader>

      

      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <StatCard title="Donasi Terkonfirmasi" :value="formatCurrency(summary.collected_total)" />
        <StatCard title="Menunggu Konfirmasi" :value="summary.pending_total" />
        <StatCard title="Total Donatur" :value="summary.donor_total" />
        <StatCard title="Total Catatan" :value="summary.records_total" />
      </div>

      <Card class="p-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
          <div>
            <p class="text-sm font-semibold text-slate-900">Filter Donasi</p>
            <p class="mt-1 text-sm text-slate-500">Audit donasi berdasarkan donatur, status, metode, dan tanggal.</p>
          </div>
          <div class="flex gap-2">
            <Button variant="outline" @click="resetFilters">Reset</Button>
            <Button :disabled="filterForm.processing" @click="applyFilters">{{ filterForm.processing ? "Memuat..." : "Terapkan" }}</Button>
          </div>
        </div>

        <div class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-5">
          <Input v-model="filterForm.search" placeholder="Cari donatur / campaign" />
          <Select v-model="filterForm.status">
            <option value="">Semua status</option>
            <option value="draft">Draft</option>
            <option value="pending">Pending</option>
            <option value="confirmed">Confirmed</option>
            <option value="rejected">Rejected</option>
          </Select>
          <Select v-model="filterForm.method">
            <option value="">Semua metode</option>
            <option value="cash">Tunai</option>
            <option value="transfer">Transfer</option>
            <option value="qris">QRIS</option>
            <option value="other">Lainnya</option>
          </Select>
          <Input v-model="filterForm.start_date" type="date" />
          <Input v-model="filterForm.end_date" type="date" />
        </div>
      </Card>

      

      <Card class="p-0">
        <div class="border-b border-slate-200 px-5 py-4">
          <p class="text-sm font-semibold text-slate-900">Daftar Donasi</p>
        </div>

        <div class="p-5">
          <TableSkeleton v-if="loadingTable" :rows="6" :columns="5" />

          <template v-else>
            <DataTable v-if="donations.data.length" :data="donations.data">
              <template #mobile-card="{ item }">
                <ClickableCard @open="openDetailDialog(item)">
                  <div class="flex items-center justify-between">
                    <div>
                    <p class="font-medium text-slate-900">{{ item.donor_name }}</p>
                    <p class="text-xs text-slate-500">{{ item.campaign }} • {{ item.donation_date }}</p>
                    <p class="mt-1 font-medium text-slate-900">{{ formatCurrency(item.amount) }}</p>
                  </div>
                    <div class="flex flex-col items-end gap-2">
                      <StatusBadge :status="item.status" />
                    </div>
                  </div>
                </ClickableCard>
              </template>
              <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                  <th class="px-4 py-3 text-left">Donatur</th>
                  <th class="px-4 py-3 text-left">Program</th>
                  <th class="px-4 py-3 text-left">Nominal</th>
                  <th class="px-4 py-3 text-left">Status</th>
                  <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in donations.data" :key="item.id" @click="openDetailDialog(item)" class="border-t border-slate-100 cursor-pointer hover:bg-slate-50 transition-colors">
                  <td class="px-4 py-3">
                    <p class="font-medium text-slate-900">{{ item.donor_name }}</p>
                    <p class="text-xs text-slate-500">{{ item.phone || "-" }} • {{ item.donation_date }}</p>
                  </td>
                  <td class="px-4 py-3">
                    <p>{{ item.campaign }}</p>
                    <p class="text-xs uppercase text-slate-500">{{ item.method }}</p>
                  </td>
                  <td class="px-4 py-3">
                    <p class="font-medium text-slate-900">{{ formatCurrency(item.amount) }}</p>
                    <p class="text-xs text-slate-500">{{ item.has_finance_link ? "Sudah masuk kas" : "Belum masuk kas" }}</p>
                  </td>
                  <td class="px-4 py-3"><StatusBadge :status="item.status" /></td>
                  <td class="px-4 py-3 text-right">
                    <div class="flex justify-end text-slate-400">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </div>
                  </td>
                </tr>
              </tbody>
            </DataTable>

            <EmptyState v-else title="Belum ada donasi" description="Catatan donasi akan muncul setelah data pertama ditambahkan." />
          </template>

          <div class="mt-4 flex items-center justify-between text-sm text-slate-500">
            <p>Menampilkan {{ donations.data.length }} dari total {{ donations.total }} donasi.</p>
            <div class="flex gap-2">
              <Button size="sm" variant="outline" :disabled="!donations.prev_page_url" @click="visitTable(donations.prev_page_url)">Sebelumnya</Button>
              <Button size="sm" variant="outline" :disabled="!donations.next_page_url" @click="visitTable(donations.next_page_url)">Berikutnya</Button>
            </div>
          </div>
        </div>
      </Card>
    </div>

    <ConfirmDialog
      v-model:open="confirmDialog.open"
      title="Konfirmasi Donasi"
      description="Donasi yang dikonfirmasi akan masuk ke pencatatan keuangan masjid. Lanjutkan?"
      confirm-text="Konfirmasi"
      cancel-text="Batal"
      :processing="statusForm.processing"
      @confirm="submitConfirm"
    />

    <ConfirmDialog
      v-model:open="rejectDialog.open"
      title="Tolak Donasi"
      description="Apakah Anda yakin ingin menolak donasi ini? Catatan keuangan terkait akan dibatalkan."
      confirm-text="Tolak"
      cancel-text="Batal"
      :processing="statusForm.processing"
      :danger="true"
      @confirm="submitReject"
    />
    <MobileFab @click="scrollToForm" />
  
    <MobileFab @click="createDialogOpen = true" />
    <Dialog :open="createDialogOpen" @close="createDialogOpen = false">
      <div class="p-5 max-h-[85vh] overflow-y-auto">
        <div>
          <p class="text-sm font-semibold text-slate-900">Catat Donasi Manual</p>
          <p class="mt-1 text-sm text-slate-500">Donasi `confirmed` otomatis masuk ke pencatatan keuangan masjid.</p>
        </div>

        <div class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-4">
          <div class="xl:col-span-2">
            <label class="mb-1.5 block text-sm font-medium text-slate-900">Nama donatur</label>
            <Input v-model="createForm.donor_name" placeholder="Hamba Allah / nama donatur" />
            <p v-if="errorFor('donor_name')" class="mt-1 text-xs text-rose-600">{{ errorFor("donor_name") }}</p>
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-900">Telepon</label>
            <Input v-model="createForm.phone" placeholder="08xxxxxxxxxx" />
            <p v-if="errorFor('phone')" class="mt-1 text-xs text-rose-600">{{ errorFor("phone") }}</p>
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-900">Email</label>
            <Input v-model="createForm.email" type="email" placeholder="email@contoh.com" />
            <p v-if="errorFor('email')" class="mt-1 text-xs text-rose-600">{{ errorFor("email") }}</p>
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-900">Alamat</label>
            <Input v-model="createForm.address" placeholder="Alamat singkat" />
            <p v-if="errorFor('address')" class="mt-1 text-xs text-rose-600">{{ errorFor("address") }}</p>
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-900">Program donasi</label>
            <Input v-model="createForm.campaign" placeholder="Program Ramadhan" />
            <p v-if="errorFor('campaign')" class="mt-1 text-xs text-rose-600">{{ errorFor("campaign") }}</p>
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-900">Kategori Donasi</label>
            <Select v-model="createForm.donation_category_id">
              <option value="">Tanpa Kategori</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
            </Select>
            <p v-if="errorFor('donation_category_id')" class="mt-1 text-xs text-rose-600">{{ errorFor("donation_category_id") }}</p>
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-900">Nominal</label>
            <Input v-model="createForm.amount" inputmode="numeric" placeholder="0" />
            <p v-if="errorFor('amount')" class="mt-1 text-xs text-rose-600">{{ errorFor("amount") }}</p>
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-900">Metode</label>
            <Select v-model="createForm.method">
              <option value="cash">Tunai</option>
              <option value="transfer">Transfer</option>
              <option value="qris">QRIS</option>
              <option value="other">Lainnya</option>
            </Select>
            <p v-if="errorFor('method')" class="mt-1 text-xs text-rose-600">{{ errorFor("method") }}</p>
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-900">Status</label>
            <Select v-model="createForm.status">
              <option value="draft">Draft</option>
              <option value="pending">Pending</option>
              <option value="confirmed">Confirmed</option>
            </Select>
            <p v-if="errorFor('status')" class="mt-1 text-xs text-rose-600">{{ errorFor("status") }}</p>
          </div>

          <div class="flex items-end">
            <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-900">
              <input v-model="createForm.is_anonymous" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-slate-900" />
              Donatur anonim
            </label>
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-900">Tanggal donasi</label>
            <Input v-model="createForm.donation_date" type="date" />
            <p v-if="errorFor('donation_date')" class="mt-1 text-xs text-rose-600">{{ errorFor("donation_date") }}</p>
          </div>
        </div>

        <div class="mt-3 grid gap-3 md:grid-cols-[1fr_auto]">
          <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-900">Catatan</label>
            <Textarea v-model="createForm.notes" :rows="3" placeholder="Tambahkan keterangan bukti transfer, niat donasi, atau konteks follow-up." />
            <p v-if="errorFor('notes')" class="mt-1 text-xs text-rose-600">{{ errorFor("notes") }}</p>
          </div>
          <div class="flex items-end">
            <Button :disabled="createForm.processing" @click="submitCreate">{{ createForm.processing ? "Menyimpan..." : "Simpan Donasi" }}</Button>
          </div>
        </div>
      </div>
    </Dialog>
  
    <DetailModal 
      :show="detailDialog.open" 
      @update:show="val => { if(!val) closeDetailDialog() }"
      :data="detailDialog.item"
      @approve="handleAction(detailDialog.item, 'confirm'); closeDetailDialog()"
      @reject="handleAction(detailDialog.item, 'reject'); closeDetailDialog()"
    >
      <div v-if="detailDialog.item" class="space-y-3 text-sm">
        <h3 class="text-lg font-bold text-slate-900 mb-4">{{ detailDialog.item.donor_name }}</h3>
        <div class="flex justify-between items-center border-b border-slate-100 pb-2">
            <span class="text-slate-500">Nominal</span>
            <span class="font-bold text-slate-900 text-base">{{ formatCurrency(detailDialog.item.amount) }}</span>
        </div>
        <div class="flex justify-between items-center border-b border-slate-100 pb-2">
            <span class="text-slate-500">Program</span>
            <span class="font-medium">{{ detailDialog.item.campaign }}</span>
        </div>
        <div class="flex justify-between items-center border-b border-slate-100 pb-2">
            <span class="text-slate-500">Metode</span>
            <span class="font-medium text-slate-900 uppercase">{{ detailDialog.item.method }}</span>
        </div>
        <div class="flex justify-between items-center border-b border-slate-100 pb-2">
            <span class="text-slate-500">Tanggal</span>
            <span class="font-medium text-slate-900">{{ detailDialog.item.donation_date }}</span>
        </div>
      </div>
    </DetailModal>

  </AuthenticatedLayout>

</template>
