<script setup>
import { ref, computed } from "vue"
import { Head, router, useForm, usePage } from "@inertiajs/vue3"
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import StatCard from "@/Components/qurban/StatCard.vue"
import Card from "@/Components/ui/card/Card.vue"
import Button from "@/Components/ui/button/Button.vue"
import DataTable from "@/Components/ui/table/DataTable.vue"
import TableSkeleton from "@/Components/ui/table/TableSkeleton.vue"
import EmptyState from "@/Components/ui/empty/EmptyState.vue"
import Input from "@/Components/ui/input/Input.vue"
import PageHeader from "@/Components/ui/page/PageHeader.vue"

const props = defineProps({
  filters: { type: Object, required: true },
  donors: { type: Object, required: true },
  summary: { type: Object, required: true },
})

const flash = computed(() => usePage().props.flash ?? {})
const loadingTable = ref(false)

const filterForm = useForm({
  search: props.filters.search ?? "",
})

const visitTable = (url, data = filterForm.data()) => {
  loadingTable.value = true
  router.get(url, data, {
    preserveState: true,
    replace: true,
    preserveScroll: true,
    onFinish: () => { loadingTable.value = false },
  })
}

const applyFilters = () => visitTable(route("donors.index"))
const resetFilters = () => { filterForm.reset(); applyFilters() }

const formatCurrency = (val) => {
  if (!val) return "Rp0"
  return "Rp" + Number(val).toLocaleString("id-ID")
}

const formatDate = (date) => {
  if (!date) return "-"
  return new Date(date).toLocaleDateString("id-ID", { year: "numeric", month: "short", day: "numeric" })
}
</script>

<template>
  <Head title="Donatur" />

  <AuthenticatedLayout title="Donatur">
    <div class="space-y-6">
      <div v-if="flash.success" class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
        {{ flash.success }}
      </div>

      <PageHeader title="Donatur" description="Data donatur masjid beserta riwayat donasi dan kontribusi mereka." />

      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <StatCard title="Total Donatur" :value="summary.total_donors" />
        <StatCard title="Donatur Aktif" :value="summary.active_donors" />
        <StatCard title="Total Donasi" :value="summary.total_donations" />
        <StatCard title="Total Terkumpul" :value="'Rp' + Number(summary.total_amount).toLocaleString('id-ID')" />
      </div>

      <Card class="p-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
          <div>
            <p class="text-sm font-semibold text-slate-900">Cari Donatur</p>
            <p class="mt-1 text-sm text-slate-500">Telusuri berdasarkan nama, telepon, atau email.</p>
          </div>
          <div class="flex gap-2">
            <Button variant="outline" @click="resetFilters">Reset</Button>
            <Button :disabled="filterForm.processing" @click="applyFilters">{{ filterForm.processing ? "Memuat..." : "Cari" }}</Button>
          </div>
        </div>
        <div class="mt-4 max-w-md">
          <Input v-model="filterForm.search" placeholder="Cari nama / telepon / email" />
        </div>
      </Card>

      <Card class="p-0">
        <div class="border-b border-slate-200 px-5 py-4">
          <p class="text-sm font-semibold text-slate-900">Daftar Donatur</p>
        </div>
        <div class="p-5">
          <TableSkeleton v-if="loadingTable" :rows="6" :columns="6" />
          <template v-else>
            <DataTable v-if="donors.data.length">
              <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                  <th class="px-4 py-3 text-left">Nama</th>
                  <th class="px-4 py-3 text-left">Telepon</th>
                  <th class="px-4 py-3 text-left">Email</th>
                  <th class="px-4 py-3 text-left">Donasi</th>
                  <th class="px-4 py-3 text-left">Total</th>
                  <th class="px-4 py-3 text-left">Donasi Terakhir</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="donor in donors.data" :key="donor.id" class="border-t border-slate-100 hover:bg-slate-50/80">
                  <td class="px-4 py-3">
                    <p class="font-medium text-slate-900">{{ donor.name }}</p>
                    <p v-if="donor.address" class="text-xs text-slate-500">{{ donor.address }}</p>
                  </td>
                  <td class="px-4 py-3 text-sm text-slate-600">{{ donor.phone || "-" }}</td>
                  <td class="px-4 py-3 text-sm text-slate-600">{{ donor.email || "-" }}</td>
                  <td class="px-4 py-3 text-sm text-slate-600">{{ donor.donation_count }}x</td>
                  <td class="px-4 py-3 text-sm font-medium text-slate-900">{{ formatCurrency(donor.total_amount) }}</td>
                  <td class="px-4 py-3 text-sm text-slate-600">{{ formatDate(donor.last_donation_date) }}</td>
                </tr>
              </tbody>
            </DataTable>
            <EmptyState v-else title="Belum ada donatur" description="Donatur akan muncul secara otomatis setelah donasi dicatat." />
          </template>
        </div>
      </Card>
    </div>
  </AuthenticatedLayout>
</template>
