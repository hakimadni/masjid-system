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
})

const rejectionForm = useForm({
  status: 'rejected',
  rejected_reason: '',
})

const rejectionDialog = ref({
  open: false,
  itemId: null,
})

const filteredCategories = computed(() =>
  props.categories.filter((item) => !createForm.entry_type || item.entry_type === createForm.entry_type)
)

const summaryCards = computed(() => [
  { title: "Total Uang Masuk", value: props.summary.income_total, type: "currency" },
  { title: "Total Uang Keluar", value: props.summary.expense_total, type: "currency" },
  { title: "Transaksi Pending", value: props.summary.pending_total, type: "plain" },
  { title: "Total Catatan", value: props.summary.records_total, type: "plain" },
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

const submitCreate = () => createForm.post(route("finance.store"), { preserveScroll: true })

const updateStatus = (id, status) =>
  useForm({ status }).patch(route("finance.update-status", id), { preserveScroll: true })

const actionItems = (item) => [
  { key: "detail", label: "Detail" },
  { key: "approve", label: "Approve", disabled: item.status === "approved" },
  { key: "reject", label: "Reject", disabled: item.status === "rejected", tone: "danger" },
]

const handleAction = (item, key) => {
  if (key === "approve") updateStatus(item.id, "approved")
  if (key === "reject") openRejectDialog(item.id)
}

const openRejectDialog = (id) => {
  rejectionDialog.value.itemId = id
  rejectionDialog.value.open = true
}

const scrollToForm = () => {
  document.getElementById('create-form')?.scrollIntoView({ behavior: 'smooth' })
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

      <div v-if="flash.success" class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
        {{ flash.success }}
      </div>

      <div class="flex gap-4 overflow-x-auto pb-4 md:grid md:grid-cols-2 xl:grid-cols-4 snap-x snap-mandatory hide-scrollbar">
        <div class="min-w-[85vw] sm:min-w-[280px] snap-center shrink-0 md:min-w-0 md:w-auto"><StatCard
          v-for="card in summaryCards"
          :key="card.title"
          :title="card.title"
          :value="card.type === 'currency' ? undefined : card.value"
        >
          <template v-if="card.type === 'currency'" #value>
            <MoneyDisplay :value="card.value" />
          </template>
        </StatCard></div>
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

      <Card id="create-form" class="p-5">
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
                <div class="group relative flex flex-col justify-between overflow-hidden rounded-3xl border border-emerald-900/5 bg-white shadow-lg shadow-emerald-900/5 ring-1 ring-slate-100/50 mb-3 p-4 transition-all active:scale-95">
                  <div class="flex items-center justify-between">
                     <div class="flex items-center gap-3">
                        <div :class="[
                           'flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl',
                           item.entry_type === 'income' ? 'bg-emerald-100 text-emerald-600' : 'bg-rose-100 text-rose-600'
                        ]">
                          <svg v-if="item.entry_type === 'income'" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3" /></svg>
                          <svg v-else class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" /></svg>
                        </div>
                        <div class="min-w-0 pr-2">
                          <p class="font-bold text-slate-800 text-sm leading-tight truncate">{{ item.title }}</p>
                          <p class="text-xs text-slate-500 font-medium mt-1 truncate">{{ item.category }} • {{ item.transaction_date }}</p>
                        </div>
                     </div>
                     <div class="text-right shrink-0">
                        <p :class="['font-bold whitespace-nowrap text-sm', item.entry_type === 'income' ? 'text-emerald-600' : 'text-slate-800']">
                          {{ item.entry_type === 'income' ? '+' : '-' }} <MoneyDisplay :value="item.amount" />
                        </p>
                        <div class="flex justify-end mt-1 items-center gap-1">
                          <StatusBadge size="sm" :status="item.status" />
                          <ActionMenu :items="actionItems(item)" @select="handleAction(item, $event)" />
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
                <tr v-for="item in financeEntries.data" :key="item.id" class="border-t border-slate-100">
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
                    <div class="flex justify-end">
                      <ActionMenu :items="actionItems(item)" @select="handleAction(item, $event)" />
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
    <MobileFab @click="scrollToForm" />
  </AuthenticatedLayout>
</template>
