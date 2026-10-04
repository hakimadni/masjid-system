<script setup>
import { Head, router, useForm } from "@inertiajs/vue3"
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import StatCard from "@/Components/qurban/StatCard.vue"
import Card from "@/Components/ui/card/Card.vue"
import Button from "@/Components/ui/button/Button.vue"
import DataTable from "@/Components/ui/table/DataTable.vue"
import EmptyState from "@/Components/ui/empty/EmptyState.vue"
import Input from "@/Components/ui/input/Input.vue"
import StatusBadge from "@/Components/ui/status/StatusBadge.vue"

const props = defineProps({
  filters: { type: Object, required: true },
  summary: { type: Object, required: true },
  recentFinance: { type: Array, required: true },
  campaignBreakdown: { type: Array, required: true },
  upcomingEvents: { type: Array, required: true },
  assetConditions: { type: Array, required: true },
})

const filterForm = useForm({
  start_date: props.filters.start_date ?? "",
  end_date: props.filters.end_date ?? "",
})

const applyFilters = () =>
  router.get(route("reports.index"), filterForm.data(), {
    preserveState: true,
    replace: true,
    preserveScroll: true,
  })

const formatCurrency = (value) =>
  new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    maximumFractionDigits: 0,
  }).format(Number(value || 0))
</script>

<template>
  <Head title="Laporan" />

  <AuthenticatedLayout title="Laporan">
    <div class="space-y-6">
      <Card class="p-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
          <div>
            <p class="text-sm font-semibold text-slate-900">Periode Laporan</p>
            <p class="mt-1 text-sm text-slate-500">Ringkasan lintas keuangan, donasi, agenda, jadwal, dan inventaris untuk pengurus inti.</p>
          </div>
          <div class="flex gap-2">
            <Input v-model="filterForm.start_date" type="date" />
            <Input v-model="filterForm.end_date" type="date" />
            <Button :disabled="filterForm.processing" @click="applyFilters">{{ filterForm.processing ? "Memuat..." : "Terapkan" }}</Button>
          </div>
        </div>
      </Card>

      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        <StatCard title="Pemasukan Disetujui" :value="formatCurrency(summary.income_total)" />
        <StatCard title="Pengeluaran Disetujui" :value="formatCurrency(summary.expense_total)" />
        <StatCard title="Donasi Terkonfirmasi" :value="formatCurrency(summary.donation_total)" />
        <StatCard title="Kegiatan Periode Ini" :value="summary.event_total" />
        <StatCard title="Total Inventaris" :value="summary.asset_total" />
        <StatCard title="Jadwal Periode Ini" :value="summary.schedule_total" />
      </div>

      <div class="grid gap-6 xl:grid-cols-2">
        <Card class="p-0">
          <div class="border-b border-slate-200 px-5 py-4">
            <p class="text-sm font-semibold text-slate-900">Transaksi Terbaru</p>
          </div>
          <div class="p-5">
            <DataTable v-if="recentFinance.length">
              <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                  <th class="px-4 py-3 text-left">Transaksi</th>
                  <th class="px-4 py-3 text-left">Tanggal</th>
                  <th class="px-4 py-3 text-left">Status</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in recentFinance" :key="item.id" class="border-t border-slate-100">
                  <td class="px-4 py-3">
                    <p class="font-medium text-slate-900">{{ item.title }}</p>
                    <p class="text-xs text-slate-500">{{ item.entry_type }} • {{ formatCurrency(item.amount) }}</p>
                  </td>
                  <td class="px-4 py-3 text-sm text-slate-600">{{ item.transaction_date }}</td>
                  <td class="px-4 py-3"><StatusBadge :status="item.status" /></td>
                </tr>
              </tbody>
            </DataTable>
            <EmptyState
              v-else
              title="Belum ada transaksi"
              description="Data transaksi pada periode terpilih akan tampil di sini."
            />
          </div>
        </Card>

        <Card class="p-0">
          <div class="border-b border-slate-200 px-5 py-4">
            <p class="text-sm font-semibold text-slate-900">Breakdown Campaign Donasi</p>
          </div>
          <div class="p-5">
            <DataTable v-if="campaignBreakdown.length">
              <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                  <th class="px-4 py-3 text-left">Campaign</th>
                  <th class="px-4 py-3 text-left">Catatan</th>
                  <th class="px-4 py-3 text-left">Total</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in campaignBreakdown" :key="item.campaign" class="border-t border-slate-100">
                  <td class="px-4 py-3 font-medium text-slate-900">{{ item.campaign }}</td>
                  <td class="px-4 py-3 text-sm text-slate-600">{{ item.total_records }} donasi</td>
                  <td class="px-4 py-3 text-sm font-medium text-slate-900">{{ formatCurrency(item.total_amount) }}</td>
                </tr>
              </tbody>
            </DataTable>
            <EmptyState
              v-else
              title="Belum ada donasi"
              description="Breakdown donasi per campaign akan ditampilkan di sini."
            />
          </div>
        </Card>
      </div>

      <div class="grid gap-6 xl:grid-cols-2">
        <Card class="p-0">
          <div class="border-b border-slate-200 px-5 py-4">
            <p class="text-sm font-semibold text-slate-900">Kegiatan Mendatang</p>
          </div>
          <div class="p-5">
            <DataTable v-if="upcomingEvents.length">
              <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                  <th class="px-4 py-3 text-left">Agenda</th>
                  <th class="px-4 py-3 text-left">Waktu</th>
                  <th class="px-4 py-3 text-left">Status</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in upcomingEvents" :key="item.id" class="border-t border-slate-100">
                  <td class="px-4 py-3">
                    <p class="font-medium text-slate-900">{{ item.title }}</p>
                    <p class="text-xs text-slate-500">{{ item.location || "-" }}</p>
                  </td>
                  <td class="px-4 py-3 text-sm text-slate-600">{{ item.start_at }}</td>
                  <td class="px-4 py-3"><StatusBadge :status="item.status" /></td>
                </tr>
              </tbody>
            </DataTable>
            <EmptyState
              v-else
              title="Tidak ada agenda mendatang"
              description="Agenda kegiatan berikutnya akan tampil di sini."
            />
          </div>
        </Card>

        <Card class="p-0">
          <div class="border-b border-slate-200 px-5 py-4">
            <p class="text-sm font-semibold text-slate-900">Kondisi Inventaris</p>
          </div>
          <div class="p-5">
            <DataTable v-if="assetConditions.length">
              <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                  <th class="px-4 py-3 text-left">Kondisi</th>
                  <th class="px-4 py-3 text-left">Jumlah</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in assetConditions" :key="item.condition" class="border-t border-slate-100">
                  <td class="px-4 py-3 text-sm font-medium text-slate-900">{{ item.condition }}</td>
                  <td class="px-4 py-3 text-sm text-slate-600">{{ item.total }}</td>
                </tr>
              </tbody>
            </DataTable>
            <EmptyState
              v-else
              title="Belum ada inventaris"
              description="Ringkasan kondisi inventaris akan muncul setelah data aset tersedia."
            />
          </div>
        </Card>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
