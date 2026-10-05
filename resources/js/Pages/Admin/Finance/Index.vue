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
import MoneyDisplay from "@/Components/ui/display/MoneyDisplay.vue"
import ActionMenu from "@/Components/ui/dropdown/ActionMenu.vue"
import PageHeader from "@/Components/ui/page/PageHeader.vue"
import Dialog from "@/Components/ui/dialog/Dialog.vue"
import ConfirmDialog from "@/Components/ui/dialog/ConfirmDialog.vue"
import MobileFab from "@/Components/MobileFab.vue"

const props = defineProps({
  filters: { type: Object, required: true },
  financeEntries: { type: Object, required: true },
  summary: { type: Object, required: true },
  accounts: { type: Array, required: true },
  categories: { type: Array, required: true },
})

const page = usePage()
const flash = computed(() => page.props.flash ?? {})
const errors = computed(() => page.props.errors ?? {})
const loadingTable = ref(false)

const filterForm = useForm({
  search: props.filters.search ?? "",
  entry_type: props.filters.entry_type ?? "",
  status: props.filters.status ?? "",
  category_id: props.filters.category_id ?? "",
  start_date: props.filters.start_date ?? "",
  end_date: props.filters.end_date ?? "",
})

const createForm = useForm({
  title: "",
  entry_type: "income",
  finance_account_id: props.accounts[0]?.id ?? "",
  finance_category_id: "",
  amount: "",
  payment_method: "cash",
  status: "pending",
  transaction_date: new Date().toISOString().slice(0, 10),
  notes: "",
  attachment: null,
})

const rejectionForm = useForm({
  status: 'rejected',
  rejected_reason: '',
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
const rejectionDialog = ref({
  open: false,
  itemId: null,
})

const filteredCategories = computed(() =>
  props.categories.filter((item) => !createForm.entry_type || item.entry_type === createForm.entry_type)
)


const formatCurrency = (value) =>
  new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    maximumFractionDigits: 0,
  }).format(Number(value || 0))

const summaryCards = computed(() => [
  { title: "Total Uang Masuk", value: formatCurrency(props.summary.income_total) },
  { title: "Total Uang Keluar", value: formatCurrency(props.summary.expense_total) },
  { title: "Transaksi Pending", value: props.summary.pending_total },
  { title: "Total Catatan", value: props.summary.records_total },
])

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

const applyFilters = () => visitTable(route("finance.index"))

const resetFilters = () => {
  filterForm.reset()
  applyFilters()
}

const handleFileUpload = (e) => {
  const file = e.target.files[0]
  if (!file) return

  // Prevent compressing non-images (like PDF if allowed later)
  if (!file.type.startsWith('image/')) {
    createForm.attachment = file
    return
  }

  const reader = new FileReader()
  reader.readAsDataURL(file)
  reader.onload = (event) => {
    const img = new Image()
    img.src = event.target.result
    img.onload = () => {
      const canvas = document.createElement('canvas')
      const MAX_WIDTH = 1200
      const MAX_HEIGHT = 1200
      let width = img.width
      let height = img.height

      if (width > height) {
        if (width > MAX_WIDTH) {
          height = Math.round(height * (MAX_WIDTH / width))
          width = MAX_WIDTH
        }
      } else {
        if (height > MAX_HEIGHT) {
          width = Math.round(width * (MAX_HEIGHT / height))
          height = MAX_HEIGHT
        }
      }

      canvas.width = width
      canvas.height = height
      const ctx = canvas.getContext('2d')
      ctx.drawImage(img, 0, 0, width, height)

      canvas.toBlob((blob) => {
        if (!blob) return
        // Create a new compressed file
        const compressedFile = new File([blob], file.name, {
          type: 'image/jpeg',
          lastModified: Date.now()
        })
        
        // If compressed is somehow bigger than original, use original
        if (compressedFile.size > file.size) {
            createForm.attachment = file
        } else {
            createForm.attachment = compressedFile
        }
      }, 'image/jpeg', 0.7) // 70% quality
    }
  }
}

const submitCreate = () => createForm.post(route("finance.store"), { preserveScroll: true, forceFormData: true, onSuccess: () => { createDialogOpen.value = false; createForm.reset() } })

const updateStatus = (id, status) =>
  useForm({ status }).patch(route("finance.update-status", id), { preserveScroll: true })

const actionItems = (item) => [
  { key: "detail", label: "Detail" },
  { key: "approve", label: "Approve", disabled: item.status === "approved" },
  { key: "reject", label: "Reject", disabled: item.status === "rejected", tone: "danger" },
]

const handleAction = (item, key) => {
  if (key === "approve") {
    updateStatus(item.id, "approved")
    closeDetailDialog()
  }
  if (key === "reject") {
    openRejectDialog(item.id)
    closeDetailDialog()
  }
}

const openRejectDialog = (id) => {
  rejectionDialog.value.itemId = id
  rejectionDialog.value.open = true
}

const scrollToForm = () => {
  createDialogOpen.value = true
}

const submitReject = () => {
  if (rejectionDialog.value.itemId) {
    rejectionForm.patch(route('finance.update-status', rejectionDialog.value.itemId), {
      preserveScroll: true,
      onSuccess: () => {
        rejectionDialog.value.open = false
        rejectionDialog.value.itemId = null
        rejectionForm.reset('rejected_reason')
      },
    })
  }
}
</script>

<template>
  <Head title="Keuangan" />

  <AuthenticatedLayout title="Keuangan">
    <div class="space-y-6">
      <PageHeader title="Keuangan" description="Kelola transaksi pemasukan dan pengeluaran dengan approval yang ketat dan jejak audit yang rapi.">
        <template #actions>
          <Button class="hidden md:inline-flex" @click="scrollToForm">Tambah Transaksi</Button>
        </template>
      </PageHeader>

      

      <div class="flex gap-4 overflow-x-auto pb-4 md:grid md:grid-cols-2 xl:grid-cols-4 snap-x snap-mandatory hide-scrollbar">
        <div class="min-w-[85vw] sm:min-w-[280px] snap-center shrink-0 md:min-w-0 md:w-auto"><StatCard
          v-for="card in summaryCards"
          :key="card.title"
          :title="card.title"
          :value="card.value"
        /></div>
      </div>

      <Card class="p-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
          <div>
            <p class="text-sm font-semibold text-slate-900">Filter Transaksi</p>
            <p class="mt-1 text-sm text-slate-500">Cari transaksi, rentang tanggal, kategori, dan status approval.</p>
          </div>
          <div class="flex gap-2">
            <Button variant="outline" @click="resetFilters">Reset</Button>
            <Button :disabled="filterForm.processing" @click="applyFilters">{{ filterForm.processing ? "Memuat..." : "Terapkan" }}</Button>
          </div>
        </div>

        <div class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-6">
          <Input v-model="filterForm.search" placeholder="Cari judul / referensi" />
          <Select v-model="filterForm.entry_type">
            <option value="">Semua jenis</option>
            <option value="income">Uang Masuk</option>
            <option value="expense">Uang Keluar</option>
          </Select>
          <Select v-model="filterForm.status">
            <option value="">Semua status</option>
            <option value="draft">Draft</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
          </Select>
          <Select v-model="filterForm.category_id">
            <option value="">Semua kategori</option>
            <option v-for="item in categories" :key="item.id" :value="item.id">{{ item.name }}</option>
          </Select>
          <Input v-model="filterForm.start_date" type="date" />
          <Input v-model="filterForm.end_date" type="date" />
        </div>
      </Card>

      

      <Card class="p-0">
        <div class="border-b border-slate-200 px-5 py-4">
          <p class="text-sm font-semibold text-slate-900">Daftar Transaksi</p>
        </div>

        <div class="p-5">
          <TableSkeleton v-if="loadingTable" :rows="6" :columns="6" />

          <template v-else>
            <DataTable v-if="financeEntries.data.length" :data="financeEntries.data">
              <template #mobile-card="{ item }">
                <div @click="openDetailDialog(item)" class="cursor-pointer group relative flex flex-col justify-between rounded-3xl border border-emerald-900/5 bg-white shadow-lg shadow-emerald-900/5 ring-1 ring-slate-100/50 mb-3 p-4 transition-all active:scale-95">
                  <div class="flex items-start justify-between gap-2">
                     <div class="flex items-start gap-3 min-w-0">
                        <div :class="[
                           'flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl',
                           item.entry_type === 'income' ? 'bg-emerald-100 text-emerald-600' : 'bg-rose-100 text-rose-600'
                        ]">
                          <svg v-if="item.entry_type === 'income'" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3" /></svg>
                          <svg v-else class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" /></svg>
                        </div>
                        <div class="min-w-0 pt-0.5">
                          <p class="font-bold text-slate-800 text-sm leading-snug break-words line-clamp-2">{{ item.title }}</p>
                          <p class="text-xs text-slate-500 font-medium mt-1 break-words line-clamp-2">{{ item.category }}<br/><span class="text-[10px] text-slate-400">{{ item.transaction_date }}</span></p>
                        </div>
                     </div>
                     <div class="text-right shrink-0 flex flex-col items-end">
                        <p :class="['font-bold whitespace-nowrap text-sm', item.entry_type === 'income' ? 'text-emerald-600' : 'text-slate-800']">
                          {{ item.entry_type === 'income' ? '+' : '-' }} <MoneyDisplay :value="item.amount" />
                        </p>
                        <div class="flex justify-end mt-1 items-center gap-1">
                          <StatusBadge size="sm" :status="item.status" />
                        </div>
                     </div>
                  </div>
                </div>
              </template>
              <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                  <th class="px-4 py-3 text-left">Transaksi</th>
                  <th class="px-4 py-3 text-left">Akun</th>
                  <th class="px-4 py-3 text-left">Kategori</th>
                  <th class="px-4 py-3 text-left">Nominal</th>
                  <th class="px-4 py-3 text-left">Status</th>
                  <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in financeEntries.data" :key="item.id" @click="openDetailDialog(item)" class="border-t border-slate-100 cursor-pointer hover:bg-slate-50 transition-colors">
                  <td class="px-4 py-3">
                    <p class="font-medium text-slate-900">{{ item.title }}</p>
                    <p class="text-xs text-slate-500">{{ item.reference_no }} • {{ item.transaction_date }}</p>
                  </td>
                  <td class="px-4 py-3">{{ item.account }}</td>
                  <td class="px-4 py-3">{{ item.category }}</td>
                  <td class="px-4 py-3">
                    <p class="font-medium text-slate-900"><MoneyDisplay :value="item.amount" /></p>
                    <p class="text-xs text-slate-500">{{ item.entry_type === "income" ? "Uang Masuk" : "Uang Keluar" }}</p>
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

            <EmptyState v-else title="Belum ada transaksi" description="Catatan keuangan akan muncul setelah transaksi pertama disimpan." />
          </template>

          <div class="mt-4 flex items-center justify-between text-sm text-slate-500">
            <p>Menampilkan {{ financeEntries.data.length }} dari total {{ financeEntries.total }} transaksi.</p>
            <div class="flex gap-2">
              <Button size="sm" variant="outline" :disabled="!financeEntries.prev_page_url" @click="visitTable(financeEntries.prev_page_url)">Sebelumnya</Button>
              <Button size="sm" variant="outline" :disabled="!financeEntries.next_page_url" @click="visitTable(financeEntries.next_page_url)">Berikutnya</Button>
            </div>
          </div>
        </div>
      </Card>
    </div>

    <ConfirmDialog
      v-model:open="rejectionDialog.open"
      title="Tolak Transaksi"
      description="Berikan alasan penolakan untuk catatan audit dan tindak lanjut bendahara."
      confirm-text="Tolak"
      cancel-text="Batal"
      :processing="rejectionForm.processing"
      :danger="true"
      @confirm="submitReject"
    >
      <Textarea v-model="rejectionForm.rejected_reason" :rows="3" placeholder="Masukkan alasan penolakan..." />
    </ConfirmDialog>
    
    <Dialog :open="detailDialog.open" @update:open="detailDialog.open = $event" @close="closeDetailDialog">
      <div class="p-5 overflow-y-auto max-h-[85vh]" v-if="detailDialog.item">
        <p class="text-sm font-semibold text-slate-900 mb-4">Detail Transaksi</p>
        
        <div class="flex gap-3 mb-6" v-if="detailDialog.item.status === 'pending'">
            <Button variant="success" class="flex-1 shadow-md" @click="handleAction(detailDialog.item, 'approve')">Setujui (Approve)</Button>
            <button class="flex-1 rounded-xl bg-rose-500 hover:bg-rose-600 text-white font-medium text-sm shadow-md shadow-rose-950/10" @click="handleAction(detailDialog.item, 'reject')">Tolak (Reject)</button>
        </div>
        <div class="flex gap-3 mb-6" v-else>
            <div class="flex-1 text-center bg-slate-50 p-3 rounded-xl border border-slate-100 flex flex-col items-center justify-center">
               <p class="text-xs text-slate-500 mb-2">Status Saat Ini</p>
               <StatusBadge :status="detailDialog.item.status" />
            </div>
        </div>

        <h3 class="text-lg font-bold text-slate-900 mb-4">{{ detailDialog.item.title }}</h3>
        <div class="space-y-3 text-sm">
            <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                <span class="text-slate-500">Nominal</span>
                <span class="font-bold text-slate-900 text-base"><MoneyDisplay :value="detailDialog.item.amount" /></span>
            </div>
            <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                <span class="text-slate-500">Tipe</span>
                <span class="font-medium" :class="detailDialog.item.entry_type === 'income' ? 'text-emerald-600' : 'text-rose-600'">
                  {{ detailDialog.item.entry_type === 'income' ? 'Uang Masuk' : 'Uang Keluar' }}
                </span>
            </div>
            <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                <span class="text-slate-500">Kategori</span>
                <span class="font-medium text-slate-900">{{ detailDialog.item.category }}</span>
            </div>
            <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                <span class="text-slate-500">Tanggal</span>
                <span class="font-medium text-slate-900">{{ detailDialog.item.transaction_date }}</span>
            </div>
            <div v-if="detailDialog.item.notes" class="pt-2">
                <span class="block text-slate-500 mb-1 text-xs">Catatan / Keterangan</span>
                <p class="text-slate-900 bg-slate-50 p-3 rounded-xl leading-relaxed">{{ detailDialog.item.notes }}</p>
            </div>
            <div v-if="detailDialog.item.attachment_url" class="pt-2">
                <span class="block text-slate-500 mb-2 text-xs">Bukti Lampiran / Nota</span>
                <a :href="detailDialog.item.attachment_url" target="_blank">
                  <img :src="detailDialog.item.attachment_url" class="w-full h-auto rounded-xl border border-slate-200 shadow-sm" alt="Bukti Transaksi" />
                </a>
            </div>
        </div>
      </div>
    </Dialog>

    <MobileFab @click="scrollToForm" />
  
    <MobileFab @click="createDialogOpen = true" />
    <Dialog :open="createDialogOpen" @close="createDialogOpen = false">
      <div class="p-5 overflow-y-auto max-h-[85vh]">
        <div>
          <p class="text-sm font-semibold text-slate-900">Catat Transaksi Baru</p>
          <p class="mt-1 text-sm text-slate-500">Gunakan status `pending` untuk proses verifikasi dan `approved` untuk pencatatan final.</p>
        </div>

        <div class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-4">
          <div class="xl:col-span-2">
            <label class="mb-1.5 block text-sm font-medium text-slate-900">Judul transaksi</label>
            <Input v-model="createForm.title" placeholder="Contoh: Pembelian alat kebersihan" />
            <p v-if="errorFor('title')" class="mt-1 text-xs text-rose-600">{{ errorFor("title") }}</p>
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-900">Jenis</label>
            <Select v-model="createForm.entry_type">
              <option value="income">Uang Masuk</option>
              <option value="expense">Uang Keluar</option>
            </Select>
            <p v-if="errorFor('entry_type')" class="mt-1 text-xs text-rose-600">{{ errorFor("entry_type") }}</p>
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-900">Akun kas</label>
            <Select v-model="createForm.finance_account_id">
              <option value="">Pilih akun kas</option>
              <option v-for="item in accounts" :key="item.id" :value="item.id">{{ item.name }}</option>
            </Select>
            <p v-if="errorFor('finance_account_id')" class="mt-1 text-xs text-rose-600">{{ errorFor("finance_account_id") }}</p>
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-900">Kategori</label>
            <Select v-model="createForm.finance_category_id">
              <option value="">Pilih kategori</option>
              <option v-for="item in filteredCategories" :key="item.id" :value="item.id">{{ item.name }}</option>
            </Select>
            <p v-if="errorFor('finance_category_id')" class="mt-1 text-xs text-rose-600">{{ errorFor("finance_category_id") }}</p>
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-900">Nominal</label>
            <Input v-model="createForm.amount" inputmode="numeric" placeholder="0" />
            <p v-if="errorFor('amount')" class="mt-1 text-xs text-rose-600">{{ errorFor("amount") }}</p>
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-900">Metode</label>
            <Select v-model="createForm.payment_method">
              <option value="cash">Tunai</option>
              <option value="transfer">Transfer</option>
              <option value="qris">QRIS</option>
              <option value="other">Lainnya</option>
            </Select>
            <p v-if="errorFor('payment_method')" class="mt-1 text-xs text-rose-600">{{ errorFor("payment_method") }}</p>
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-900">Status</label>
            <Select v-model="createForm.status">
              <option value="draft">Draft</option>
              <option value="pending">Pending</option>
              <option value="approved">Approved</option>
            </Select>
            <p v-if="errorFor('status')" class="mt-1 text-xs text-rose-600">{{ errorFor("status") }}</p>
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-900">Tanggal transaksi</label>
            <Input v-model="createForm.transaction_date" type="date" />
            <p v-if="errorFor('transaction_date')" class="mt-1 text-xs text-rose-600">{{ errorFor("transaction_date") }}</p>
          </div>
        </div>

        <div class="mt-3 grid gap-3 md:grid-cols-[1fr_auto]">
          <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-900">Catatan</label>
            <Textarea v-model="createForm.notes" :rows="3" placeholder="Tambahkan vendor, kebutuhan approval, atau detail transaksi." />
            <p v-if="errorFor('notes')" class="mt-1 text-xs text-rose-600">{{ errorFor("notes") }}</p>
          </div>
          <div class="flex items-end">
            <Button :disabled="createForm.processing" @click="submitCreate">{{ createForm.processing ? "Menyimpan..." : "Simpan Transaksi" }}</Button>
          </div>
        </div>
      </div>
    </Dialog>
  </AuthenticatedLayout>

</template>
