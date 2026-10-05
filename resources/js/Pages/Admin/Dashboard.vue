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
      
      <!-- Action-Oriented Bento Grid (Main Entry Points) -->
      <div>
        <p class="text-sm font-semibold text-slate-500 mb-3 uppercase tracking-wider">Aksi Cepat</p>
        <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
          <!-- Modul Keuangan (2 cols on mobile) -->
          <Link :href="route('finance.index')" class="col-span-1 group relative flex flex-col justify-between overflow-hidden rounded-[2rem] bg-gradient-to-br from-emerald-500 to-emerald-700 p-5 sm:p-6 text-white shadow-xl shadow-emerald-900/10 active:scale-[0.98] transition-all border border-emerald-400/30 hover:shadow-emerald-900/20">
            <div class="flex justify-between items-start mb-6">
              <div class="rounded-2xl bg-white/20 p-3 sm:p-4 backdrop-blur-md">
                <svg class="h-7 w-7 sm:h-8 sm:w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
              </div>
              <svg class="h-5 w-5 text-emerald-200 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
            </div>
            <div>
              <p class="text-xs sm:text-sm text-emerald-100 font-medium mb-1">Pencatatan Kas</p>
              <h3 class="text-lg sm:text-xl font-bold tracking-tight">Uang Masuk</h3>
            </div>
          </Link>

          <Link :href="route('finance.index')" class="col-span-1 group relative flex flex-col justify-between overflow-hidden rounded-[2rem] bg-gradient-to-br from-rose-500 to-rose-700 p-5 sm:p-6 text-white shadow-xl shadow-rose-900/10 active:scale-[0.98] transition-all border border-rose-400/30 hover:shadow-rose-900/20">
            <div class="flex justify-between items-start mb-6">
              <div class="rounded-2xl bg-white/20 p-3 sm:p-4 backdrop-blur-md">
                <svg class="h-7 w-7 sm:h-8 sm:w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4" /></svg>
              </div>
              <svg class="h-5 w-5 text-rose-200 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
            </div>
            <div>
              <p class="text-xs sm:text-sm text-rose-100 font-medium mb-1">Pencatatan Kas</p>
              <h3 class="text-lg sm:text-xl font-bold tracking-tight">Uang Keluar</h3>
            </div>
          </Link>

          <!-- Modul ZISWAF & Qurban (1 col on mobile, 2 cols on desktop) -->
          <Link :href="route('donations.index')" class="col-span-1 group relative flex flex-col items-center justify-center gap-3 overflow-hidden rounded-[2rem] bg-white p-4 sm:p-6 text-slate-800 shadow-lg shadow-slate-200/50 active:scale-[0.98] transition-all border border-slate-100 hover:border-blue-200 hover:bg-blue-50/50">
            <div class="rounded-2xl bg-blue-100 p-3 text-blue-600">
              <svg class="h-6 w-6 sm:h-8 sm:w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
            </div>
            <span class="text-xs sm:text-sm font-bold tracking-wide text-center">Donasi & ZISWAF</span>
          </Link>

          <Link :href="route('qurban.index')" class="col-span-1 group relative flex flex-col items-center justify-center gap-3 overflow-hidden rounded-[2rem] bg-white p-4 sm:p-6 text-slate-800 shadow-lg shadow-slate-200/50 active:scale-[0.98] transition-all border border-slate-100 hover:border-amber-200 hover:bg-amber-50/50">
            <div class="rounded-2xl bg-amber-100 p-3 text-amber-600">
              <svg class="h-6 w-6 sm:h-8 sm:w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" /></svg>
            </div>
            <span class="text-xs sm:text-sm font-bold tracking-wide text-center">Program Qurban</span>
          </Link>

          <!-- Modul Operasional (1 col on mobile) -->
          <Link :href="route('schedules.index')" class="col-span-1 group relative flex flex-col items-center justify-center gap-3 overflow-hidden rounded-[2rem] bg-white p-4 sm:p-6 text-slate-800 shadow-lg shadow-slate-200/50 active:scale-[0.98] transition-all border border-slate-100 hover:border-slate-300 hover:bg-slate-50/50">
            <div class="rounded-xl bg-white p-2.5 shadow-sm text-sky-500">
              <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            </div>
            <span class="text-xs sm:text-sm font-bold tracking-wide text-center mt-1">Jadwal Imam</span>
          </Link>

          <Link :href="route('announcements.index')" class="col-span-1 group relative flex flex-col items-center justify-center gap-3 overflow-hidden rounded-[2rem] bg-white p-4 sm:p-6 text-slate-800 shadow-lg shadow-slate-200/50 active:scale-[0.98] transition-all border border-slate-100 hover:border-slate-300 hover:bg-slate-50/50">
            <div class="rounded-xl bg-white p-2.5 shadow-sm text-indigo-500">
              <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" /></svg>
            </div>
            <span class="text-xs sm:text-sm font-bold tracking-wide text-center mt-1">Buat Info</span>
          </Link>

          <Link :href="route('events.index')" class="col-span-1 group relative flex flex-col items-center justify-center gap-3 overflow-hidden rounded-[2rem] bg-white p-4 sm:p-6 text-slate-800 shadow-lg shadow-slate-200/50 active:scale-[0.98] transition-all border border-slate-100 hover:border-slate-300 hover:bg-slate-50/50">
            <div class="rounded-xl bg-white p-2.5 shadow-sm text-purple-500">
              <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
            </div>
            <span class="text-xs sm:text-sm font-bold tracking-wide text-center mt-1">Agenda Masjid</span>
          </Link>

          <Link :href="route('jamaahs.index')" class="col-span-1 group relative flex flex-col items-center justify-center gap-3 overflow-hidden rounded-[2rem] bg-white p-4 sm:p-6 text-slate-800 shadow-lg shadow-slate-200/50 active:scale-[0.98] transition-all border border-slate-100 hover:border-slate-300 hover:bg-slate-50/50">
            <div class="rounded-xl bg-white p-2.5 shadow-sm text-teal-500">
              <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" /></svg>
            </div>
            <span class="text-xs sm:text-sm font-bold tracking-wide text-center mt-1">Data Jamaah</span>
          </Link>
        </div>
      </div>

      <!-- Summary Data / Stat Cards -->
      <div class="mt-8">
        <p class="text-sm font-semibold text-slate-500 mb-3 uppercase tracking-wider">Ringkasan Data</p>
        <div class="flex gap-4 overflow-x-auto pb-4 md:grid md:grid-cols-2 xl:grid-cols-3 snap-x snap-mandatory hide-scrollbar">
          <div v-for="card in cards" :key="card.label" class="min-w-[85vw] sm:min-w-[280px] snap-center shrink-0 md:min-w-0 md:w-auto"><StatCard :title="card.label" :value="formatValue(card)" /></div>
        </div>
      </div>

      <!-- Charts Section (Desktop Only) -->
      <div class="hidden md:grid gap-6 xl:grid-cols-2">
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



      <div class="hidden md:grid gap-6 xl:grid-cols-2">
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

      <div class="hidden md:grid gap-6 xl:grid-cols-2">
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
