<script setup>
import { computed, ref } from "vue"
import { Head, router, useForm, usePage } from "@inertiajs/vue3"
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import StatCard from "@/Components/qurban/StatCard.vue"
import Card from "@/Components/ui/card/Card.vue"
import Button from "@/Components/ui/button/Button.vue"
import DataTable from "@/Components/ui/table/DataTable.vue"
import EmptyState from "@/Components/ui/empty/EmptyState.vue"
import Input from "@/Components/ui/input/Input.vue"
import Select from "@/Components/ui/select/Select.vue"
import Textarea from "@/Components/ui/textarea/Textarea.vue"
import StatusBadge from "@/Components/ui/status/StatusBadge.vue"
import ActionMenu from "@/Components/ui/dropdown/ActionMenu.vue"
import PageHeader from "@/Components/ui/page/PageHeader.vue"

const props = defineProps({
  filters: { type: Object, required: true },
  categories: { type: Object, required: true },
  summary: { type: Object, required: true },
})

const page = usePage()
const flash = computed(() => page.props.flash ?? {})
const errors = computed(() => page.props.errors ?? {})
const editingId = ref(null)

const filterForm = useForm({
  search: props.filters.search ?? "",
  entry_type: props.filters.entry_type ?? "",
  status: props.filters.status ?? "",
})

const categoryForm = useForm({
  name: "",
  entry_type: "income",
  description: "",
  is_active: true,
})

const applyFilters = () => filterForm.get(route("finance-categories.index"), { preserveState: true, replace: true, preserveScroll: true })
const resetFilters = () => { filterForm.reset(); applyFilters() }

const submitForm = () => {
  if (editingId.value) {
    categoryForm.put(route("finance-categories.update", editingId.value), { preserveScroll: true, onSuccess: resetEditor })
    return
  }

  categoryForm.post(route("finance-categories.store"), { preserveScroll: true, onSuccess: resetEditor })
}

const resetEditor = () => {
  editingId.value = null
  categoryForm.reset()
  categoryForm.entry_type = "income"
  categoryForm.is_active = true
}

const editCategory = (item) => {
  editingId.value = item.id
  categoryForm.name = item.name
  categoryForm.entry_type = item.entry_type
  categoryForm.description = item.description ?? ""
  categoryForm.is_active = item.is_active
}

const actionItems = [{ key: "edit", label: "Edit" }]
const handleAction = (item, key) => { if (key === "edit") editCategory(item) }
const errorFor = (key) => errors.value[key] ?? ""
</script>

<template>
  <Head title="Kategori Keuangan" />

  <AuthenticatedLayout title="Kategori Keuangan">
    <div class="space-y-6">
      <PageHeader title="Kategori Keuangan" description="Kelola kategori pemasukan dan pengeluaran agar pencatatan keuangan lebih konsisten." />

      <div v-if="flash.success" class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ flash.success }}</div>

      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <StatCard title="Total Kategori" :value="summary.records_total" />
        <StatCard title="Aktif" :value="summary.active_total" />
        <StatCard title="Pemasukan" :value="summary.income_total" />
        <StatCard title="Pengeluaran" :value="summary.expense_total" />
      </div>

      <Card class="p-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
          <div>
            <p class="text-sm font-semibold text-slate-900">Filter Kategori</p>
            <p class="mt-1 text-sm text-slate-500">Cari kategori berdasarkan nama, jenis, dan status aktif.</p>
          </div>
          <div class="flex gap-2">
            <Button variant="outline" @click="resetFilters">Reset</Button>
            <Button @click="applyFilters">Terapkan</Button>
          </div>
        </div>
        <div class="mt-4 grid gap-3 md:grid-cols-3">
          <Input v-model="filterForm.search" placeholder="Cari kategori" />
          <Select v-model="filterForm.entry_type">
            <option value="">Semua jenis</option>
            <option value="income">Pemasukan</option>
            <option value="expense">Pengeluaran</option>
          </Select>
          <Select v-model="filterForm.status">
            <option value="">Semua status</option>
            <option value="active">Aktif</option>
            <option value="inactive">Tidak aktif</option>
          </Select>
        </div>
      </Card>

      <Card class="p-5">
        <div>
          <p class="text-sm font-semibold text-slate-900">{{ editingId ? 'Edit Kategori' : 'Tambah Kategori' }}</p>
          <p class="mt-1 text-sm text-slate-500">Gunakan kategori aktif agar muncul di form transaksi.</p>
        </div>
        <div class="mt-4 grid gap-3 md:grid-cols-2">
          <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-900">Nama</label>
            <Input v-model="categoryForm.name" placeholder="Contoh: Operasional Masjid" />
            <p v-if="errorFor('name')" class="mt-1 text-xs text-rose-600">{{ errorFor('name') }}</p>
          </div>
          <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-900">Jenis</label>
            <Select v-model="categoryForm.entry_type">
              <option value="income">Pemasukan</option>
              <option value="expense">Pengeluaran</option>
            </Select>
            <p v-if="errorFor('entry_type')" class="mt-1 text-xs text-rose-600">{{ errorFor('entry_type') }}</p>
          </div>
          <div class="md:col-span-2">
            <label class="mb-1.5 block text-sm font-medium text-slate-900">Deskripsi</label>
            <Textarea v-model="categoryForm.description" :rows="3" placeholder="Tambahkan penggunaan kategori ini." />
          </div>
          <label class="inline-flex items-center gap-2 text-sm text-slate-700">
            <input v-model="categoryForm.is_active" type="checkbox" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" />
            Kategori aktif
          </label>
        </div>
        <div class="mt-4 flex justify-end gap-2">
          <Button v-if="editingId" variant="outline" @click="resetEditor">Batal Edit</Button>
          <Button :disabled="categoryForm.processing" @click="submitForm">{{ categoryForm.processing ? 'Menyimpan...' : (editingId ? 'Simpan Perubahan' : 'Tambah Kategori') }}</Button>
        </div>
      </Card>

      <Card class="p-0">
        <div class="border-b border-slate-200 px-5 py-4"><p class="text-sm font-semibold text-slate-900">Daftar Kategori</p></div>
        <div class="p-5">
          <DataTable v-if="categories.data.length">
            <thead class="bg-slate-50 text-xs uppercase text-slate-500">
              <tr>
                <th class="px-4 py-3 text-left">Kategori</th>
                <th class="px-4 py-3 text-left">Jenis</th>
                <th class="px-4 py-3 text-left">Status</th>
                <th class="px-4 py-3 text-left">Transaksi</th>
                <th class="px-4 py-3 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in categories.data" :key="item.id" class="border-t border-slate-100">
                <td class="px-4 py-3"><p class="font-medium text-slate-900">{{ item.name }}</p><p v-if="item.description" class="text-xs text-slate-500">{{ item.description }}</p></td>
                <td class="px-4 py-3 text-sm text-slate-600">{{ item.entry_type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}</td>
                <td class="px-4 py-3"><StatusBadge :status="item.is_active ? 'published' : 'draft'" /></td>
                <td class="px-4 py-3 text-sm text-slate-600">{{ item.transactions_count }}</td>
                <td class="px-4 py-3 text-right"><div class="flex justify-end"><ActionMenu :items="actionItems" @select="handleAction(item, $event)" /></div></td>
              </tr>
            </tbody>
          </DataTable>
          <EmptyState v-else title="Belum ada kategori" description="Tambahkan kategori pemasukan dan pengeluaran agar laporan lebih rapi." />
        </div>
      </Card>
    </div>
  </AuthenticatedLayout>
</template>
