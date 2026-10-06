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
import Download from "lucide-vue-next/dist/esm/icons/download.js"

// ChartJS imports
import { Bar, Line, Doughnut } from 'vue-chartjs'
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
  ArcElement
} from 'chart.js'

ChartJS.register(
  Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, PointElement, LineElement, ArcElement
)

const chartOptionsCurrency = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { display: true },
    tooltip: {
      callbacks: {
        label: function(context) {
          let label = context.dataset.label || '';
          if (label) label += ': ';
          if (context.parsed.y !== null) {
            label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(context.parsed.y);
          }
          return label;
        }
      }
    }
  },
  scales: {
    y: {
      ticks: {
        callback: function(value) {
          return new Intl.NumberFormat('id-ID', { notation: "compact" }).format(value);
        }
      }
    }
  }
}

const chartOptionsPie = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { position: 'bottom' }
  }
}

const props = defineProps({
  filters: { type: Object, required: true },
  summary: { type: Object, required: true },
  chartData: { type: Object, required: true },
  recentFinance: { type: Array, required: true },
  campaignBreakdown: { type: Array, required: true },
  upcomingEvents: { type: Array, required: true },
  assetConditions: { type: Array, required: true },
})

const filterForm = useForm({
  start_date: props.filters.start_date ?? "",
  end_date: props.filters.end_date ?? "",
})

const applyFilters = () => {
  router.get(route("reports.index"), filterForm.data(), {
    preserveState: true,
    replace: true,
    preserveScroll: true,
  })
}

const exportUrl = () => {
  const url = new URL(route('reports.export'));
  if (filterForm.start_date) url.searchParams.append('start_date', filterForm.start_date);
  if (filterForm.end_date) url.searchParams.append('end_date', filterForm.end_date);
  return url.toString();
}

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
        <Card class="flex flex-col p-0">
          <div class="border-b border-slate-200 px-5 py-4">
            <p class="text-sm font-semibold text-slate-900">Tren Pemasukan & Pengeluaran</p>
            <p class="text-xs text-slate-500 mt-1">Grafik kinerja keuangan dalam 6 bulan terakhir</p>
          </div>
          <div class="flex-1 flex flex-col p-5">
            <div class="h-[250px] w-full mb-6">
                <Bar v-if="chartData.keuangan_bulanan" :data="chartData.keuangan_bulanan" :options="chartOptionsCurrency" />
            </div>
            <div class="mt-auto">
                <Button as="a" :href="exportUrl()" variant="outline" class="w-full">
                    <Download class="mr-2 h-4 w-4" /> Download Excel Keuangan
                </Button>
            </div>
          </div>
        </Card>

        <Card class="flex flex-col p-0">
          <div class="border-b border-slate-200 px-5 py-4">
            <p class="text-sm font-semibold text-slate-900">Breakdown Campaign Donasi</p>
            <p class="text-xs text-slate-500 mt-1">Distribusi dana donasi menurut program (Top 5)</p>
          </div>
          <div class="flex-1 flex flex-col p-5">
            <div class="h-[250px] w-full mb-6">
                <Doughnut v-if="chartData.donasi_campaign" :data="chartData.donasi_campaign" :options="chartOptionsPie" />
            </div>
            <div class="mt-auto">
                <Button as="a" :href="exportUrl() + '&type=donasi'" variant="outline" class="w-full">
                    <Download class="mr-2 h-4 w-4" /> Download Excel Donasi
                </Button>
            </div>
          </div>
        </Card>
      </div>

      <div class="grid gap-6 xl:grid-cols-2">
        <Card class="flex flex-col p-0">
          <div class="border-b border-slate-200 px-5 py-4">
            <p class="text-sm font-semibold text-slate-900">Kondisi Inventaris Aset</p>
            <p class="text-xs text-slate-500 mt-1">Rangkuman kondisi fasilitas masjid</p>
          </div>
          <div class="flex-1 flex flex-col p-5">
            <div class="h-[250px] w-full mb-6">
                <Doughnut v-if="chartData.aset_kondisi" :data="chartData.aset_kondisi" :options="chartOptionsPie" />
            </div>
            <div class="mt-auto">
                <Button as="a" :href="exportUrl() + '&type=aset'" variant="outline" class="w-full">
                    <Download class="mr-2 h-4 w-4" /> Download Laporan Aset
                </Button>
            </div>
          </div>
        </Card>

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
      </div>
    </div>
  </AuthenticatedLayout>
</template>
