<script setup>
import { computed } from "vue"
import { Head, Link } from "@inertiajs/vue3"
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import StatCard from "@/Components/qurban/StatCard.vue"
import Card from "@/Components/ui/card/Card.vue"
import Button from "@/Components/ui/button/Button.vue"
import DataTable from "@/Components/ui/table/DataTable.vue"
import EmptyState from "@/Components/ui/empty/EmptyState.vue"
import StatusBadge from "@/Components/ui/status/StatusBadge.vue"

import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
} from 'chart.js'
import { Bar, Line } from 'vue-chartjs'

ChartJS.register(CategoryScale, LinearScale, BarElement, PointElement, LineElement, Title, Tooltip, Legend)

const props = defineProps({
  cards: { type: Array, required: true },
  financeChart: { type: Object, required: true },
  donationChart: { type: Object, required: true },
  recentFinance: { type: Array, required: true },
  recentDonations: { type: Array, required: true },
  upcomingSchedules: { type: Array, required: true },
  activeAnnouncements: { type: Array, required: true },
})

const formatCurrency = (value) =>
  new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    maximumFractionDigits: 0,
  }).format(Number(value || 0))

const formatValue = (card) => (card.type === "currency" ? formatCurrency(card.value) : card.value)

const financeChartData = computed(() => ({
  labels: props.financeChart.labels,
  datasets: [
    {
      label: 'Pemasukan',
      backgroundColor: '#10b981', // emerald-500
      data: props.financeChart.income,
      borderRadius: 4,
    },
    {
      label: 'Pengeluaran',
      backgroundColor: '#f43f5e', // rose-500
      data: props.financeChart.expense,
      borderRadius: 4,
    }
  ]
}))

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { position: 'bottom' }
  },
  scales: {
    y: { beginAtZero: true }
  }
}

const donationChartData = computed(() => {
  const colors = ['#3b82f6', '#8b5cf6', '#f59e0b', '#06b6d4', '#14b8a6']
  return {
    labels: props.donationChart.labels,
    datasets: props.donationChart.datasets.map((ds, i) => ({
      ...ds,
      borderColor: colors[i % colors.length],
      backgroundColor: colors[i % colors.length],
      borderWidth: 2,
      tension: 0.3,
    }))
  }
})
</script>

<template>
  <Head title="Executive Dashboard" />

  <AuthenticatedLayout title="Executive Dashboard">
    <div class="space-y-6">
      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        <StatCard v-for="card in cards" :key="card.label" :title="card.label" :value="formatValue(card)" />
      </div>

      <!-- Charts Section -->
      <div class="grid gap-6 xl:grid-cols-2">
        <Card class="p-0">
          <div class="border-b border-slate-200 px-5 py-4">
            <p class="text-sm font-semibold text-slate-900">Arus Kas (6 Bulan Terakhir)</p>
          </div>
          <div class="p-5 h-[350px]">
            <Bar :data="financeChartData" :options="chartOptions" />
          </div>
        </Card>
        <Card class="p-0">
          <div class="border-b border-slate-200 px-5 py-4">
            <p class="text-sm font-semibold text-slate-900">Tren Donasi (6 Bulan Terakhir)</p>
          </div>
          <div class="p-5 h-[350px]">
            <Line :data="donationChartData" :options="chartOptions" />
          </div>
        </Card>
      </div>

      <Card class="p-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
          <div>
            <p class="text-sm font-semibold text-slate-900">Aksi Cepat Harian</p>
            <p class="mt-1 text-sm text-slate-500">Akses modul yang paling sering dipakai pengurus masjid.</p>
          </div>
          <div class="flex flex-wrap gap-2">
            <Link :href="route('finance.index')"><Button variant="outline">Keuangan</Button></Link>
            <Link :href="route('donations.index')"><Button variant="outline">Donasi</Button></Link>
            <Link :href="route('schedules.index')"><Button variant="outline">Jadwal</Button></Link>
          </div>
        </div>
      </Card>

      <div class="grid gap-6 xl:grid-cols-2">
        <Card class="p-0">
          <div class="border-b border-slate-200 px-5 py-4">
            <p class="text-sm font-semibold text-slate-900">Transaksi Terbaru</p>
          </div>
          <div class="p-5">
            <DataTable v-if="recentFinance.length">
              <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                  <th class="px-4 py-3 text-left">Judul</th>
                  <th class="px-4 py-3 text-left">Kategori</th>
                  <th class="px-4 py-3 text-left">Nominal</th>
                  <th class="px-4 py-3 text-left">Status</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in recentFinance" :key="item.id" class="border-t border-slate-100">
                  <td class="px-4 py-3">
                    <p class="font-medium text-slate-900">{{ item.title }}</p>
                    <p class="text-xs text-slate-500">{{ item.reference_no }}</p>
                  </td>
                  <td class="px-4 py-3">{{ item.category || "-" }}</td>
                  <td class="px-4 py-3">{{ formatCurrency(item.amount) }}</td>
                  <td class="px-4 py-3"><StatusBadge :status="item.status" /></td>
                </tr>
              </tbody>
            </DataTable>
            <EmptyState v-else title="Belum ada transaksi" description="Catatan pemasukan dan pengeluaran akan muncul di sini." />
          </div>
        </Card>

        <Card class="p-0">
          <div class="border-b border-slate-200 px-5 py-4">
            <p class="text-sm font-semibold text-slate-900">Donasi Terbaru</p>
          </div>
          <div class="space-y-3 p-5">
            <div v-if="recentDonations.length === 0">
              <EmptyState title="Belum ada donasi" description="Donasi manual yang dicatat akan tampil di panel ini." />
            </div>
            <div v-for="item in recentDonations" :key="item.id" class="rounded-2xl border border-slate-200 px-4 py-3">
              <div class="flex items-start justify-between gap-3">
                <div>
                  <p class="font-medium text-slate-900">{{ item.donor_name }}</p>
                  <p class="text-sm text-slate-500">{{ item.campaign }}</p>
                </div>
                <StatusBadge :status="item.status" />
              </div>
              <p class="mt-3 text-sm text-slate-700">{{ formatCurrency(item.amount) }}</p>
            </div>
          </div>
        </Card>
      </div>

      <div class="grid gap-6 xl:grid-cols-2">
        <Card class="p-0">
          <div class="border-b border-slate-200 px-5 py-4">
            <p class="text-sm font-semibold text-slate-900">Jadwal Terdekat</p>
          </div>
          <div class="space-y-3 p-5">
            <div v-if="upcomingSchedules.length === 0">
              <EmptyState title="Belum ada jadwal" description="Jadwal shalat dan petugas yang akan datang akan muncul di sini." />
            </div>
            <div v-for="item in upcomingSchedules" :key="item.id" class="rounded-2xl border border-slate-200 px-4 py-3">
              <div class="flex items-start justify-between gap-3">
                <div>
                  <p class="font-medium text-slate-900">{{ item.title }}</p>
                  <p class="text-sm text-slate-500">{{ item.subtitle || "-" }}</p>
                </div>
                <StatusBadge :status="item.status" />
              </div>
              <p class="mt-3 text-sm text-slate-700">{{ item.scheduled_at }}</p>
            </div>
          </div>
        </Card>

        <Card class="p-0">
          <div class="border-b border-slate-200 px-5 py-4">
            <p class="text-sm font-semibold text-slate-900">Pengumuman Aktif</p>
          </div>
          <div class="space-y-3 p-5">
            <div v-if="activeAnnouncements.length === 0">
              <EmptyState title="Belum ada pengumuman" description="Pengumuman yang dipublikasikan akan tampil di panel ini." />
            </div>
            <div v-for="item in activeAnnouncements" :key="item.id" class="rounded-2xl border border-slate-200 px-4 py-3">
              <p class="font-medium text-slate-900">{{ item.title }}</p>
              <p class="mt-2 text-sm text-slate-500">{{ item.published_at || "Belum dijadwalkan" }}</p>
            </div>
          </div>
        </Card>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
