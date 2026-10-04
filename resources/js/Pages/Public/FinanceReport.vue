<script setup>
import { Head, Link } from "@inertiajs/vue3"

const props = defineProps({
  mosque: { type: Object, default: null },
  financeData: { type: Object, default: null },
  error: { type: String, default: null },
})

const formatCurrency = (value) =>
  new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    maximumFractionDigits: 0,
  }).format(Number(value || 0))

const formatMonth = (item) => {
  if (!item) return '-'
  const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des']
  return `${months[item.month - 1]} ${item.year}`
}
</script>

<template>
  <Head :title="'Transparansi Keuangan - ' + (mosque?.name || 'MasjidOS')" />

  <div class="min-h-screen bg-slate-900 font-sans selection:bg-emerald-500 selection:text-white pb-20">
    <!-- Navbar -->
    <header class="fixed top-0 left-0 right-0 z-50 bg-slate-900/80 backdrop-blur-md border-b border-white/10">
      <div class="container mx-auto px-6 py-4 flex items-center justify-between">
        <nav class="flex gap-4 items-center">
          <Link href="/" class="text-sm font-medium text-emerald-400 hover:text-emerald-300 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Beranda
          </Link>
        </nav>
        <div class="w-8 h-8 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-lg flex items-center justify-center shadow-lg">
          <span class="text-white font-bold">م</span>
        </div>
      </div>
    </header>

    <main class="container mx-auto px-6 pt-32 max-w-5xl space-y-12">
      <!-- Header -->
      <div class="text-center relative">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-72 h-72 bg-emerald-500/20 rounded-full blur-[100px] -z-10 pointer-events-none"></div>
        <h1 class="text-4xl md:text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-emerald-300 to-teal-500 mb-4">Transparansi Keuangan</h1>
        <p class="text-lg text-slate-400 font-light max-w-2xl mx-auto">
          Laporan rekapitulasi dana umat yang dikelola oleh {{ mosque?.name || 'masjid kami' }} dengan penuh integritas.
        </p>
      </div>

      <div v-if="error" class="bg-amber-900/40 border border-amber-500/30 rounded-2xl p-6 backdrop-blur-sm">
        <p class="text-amber-400 font-medium flex items-center gap-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
          {{ error }}
        </p>
      </div>

      <div v-else class="space-y-10">
        <!-- Summary Cards -->
        <div class="grid md:grid-cols-3 gap-6">
          <div class="bg-slate-800/40 backdrop-blur-md border border-white/5 rounded-3xl p-8 relative overflow-hidden group">
            <div class="absolute inset-0 bg-emerald-500/5 translate-y-full group-hover:translate-y-0 transition-transform"></div>
            <p class="text-sm font-medium text-slate-400 mb-2 uppercase tracking-wider">Total Pemasukan</p>
            <p class="text-3xl font-bold text-emerald-400">{{ formatCurrency(financeData?.totals?.income || 0) }}</p>
          </div>
          <div class="bg-slate-800/40 backdrop-blur-md border border-white/5 rounded-3xl p-8 relative overflow-hidden group">
            <div class="absolute inset-0 bg-rose-500/5 translate-y-full group-hover:translate-y-0 transition-transform"></div>
            <p class="text-sm font-medium text-slate-400 mb-2 uppercase tracking-wider">Total Pengeluaran</p>
            <p class="text-3xl font-bold text-rose-400">{{ formatCurrency(financeData?.totals?.expense || 0) }}</p>
          </div>
          <div class="bg-gradient-to-br from-emerald-900 to-slate-900 border border-emerald-500/20 rounded-3xl p-8 relative overflow-hidden">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-emerald-500/30 rounded-full blur-[30px]"></div>
            <p class="text-sm font-medium text-emerald-100/70 mb-2 uppercase tracking-wider">Total Saldo Kas</p>
            <p class="text-3xl font-black text-transparent bg-clip-text bg-gradient-to-r from-emerald-300 to-teal-500 relative z-10">
              {{ formatCurrency(financeData?.totals?.balance || 0) }}
            </p>
          </div>
        </div>

        <!-- Monthly Summary -->
        <div class="bg-slate-800/40 backdrop-blur-md border border-white/5 rounded-3xl p-8">
          <h2 class="text-xl font-bold text-white mb-6">Riwayat Kas Bulanan</h2>
          <div class="overflow-x-auto rounded-xl border border-white/5 bg-slate-900/50">
            <table class="w-full text-sm">
              <thead class="bg-slate-800/80">
                <tr>
                  <th class="text-left py-4 px-6 font-semibold text-slate-300">Bulan</th>
                  <th class="text-right py-4 px-6 font-semibold text-slate-300">Pemasukan</th>
                  <th class="text-right py-4 px-6 font-semibold text-slate-300">Pengeluaran</th>
                  <th class="text-right py-4 px-6 font-semibold text-slate-300">Surplus/Defisit</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-white/5">
                <tr v-for="month in financeData?.monthly" :key="month.month + month.year" class="hover:bg-slate-800/50 transition-colors">
                  <td class="py-4 px-6 text-slate-200 font-medium">{{ formatMonth(month) }}</td>
                  <td class="py-4 px-6 text-right text-emerald-400">{{ formatCurrency(month.income) }}</td>
                  <td class="py-4 px-6 text-right text-rose-400">{{ formatCurrency(month.expense) }}</td>
                  <td class="py-4 px-6 text-right font-bold" :class="(month.income - month.expense) >= 0 ? 'text-emerald-400' : 'text-rose-400'">
                    {{ formatCurrency(month.income - month.expense) }}
                  </td>
                </tr>
                <tr v-if="!financeData?.monthly?.length">
                  <td colspan="4" class="py-12 text-center text-slate-500 italic">Belum ada catatan pembukuan</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Category Breakdown -->
        <div class="grid lg:grid-cols-2 gap-8">
          <div class="bg-slate-800/40 backdrop-blur-md border border-white/5 rounded-3xl p-8">
            <h3 class="font-bold text-white mb-6 text-lg flex items-center gap-2">
              <span class="w-3 h-3 rounded-full bg-emerald-400"></span> Pemasukan per Kategori
            </h3>
            <div class="space-y-4">
              <div v-for="cat in financeData?.income_by_category" :key="cat.category" class="flex justify-between items-center p-4 rounded-xl bg-slate-900/50 border border-white/5 hover:border-emerald-500/20 transition-colors">
                <span class="text-slate-300">{{ cat.category }}</span>
                <span class="font-bold text-emerald-400">{{ formatCurrency(cat.total) }}</span>
              </div>
              <p v-if="!financeData?.income_by_category?.length" class="text-slate-500 text-center py-6">Tidak ada data pemasukan</p>
            </div>
          </div>

          <div class="bg-slate-800/40 backdrop-blur-md border border-white/5 rounded-3xl p-8">
            <h3 class="font-bold text-white mb-6 text-lg flex items-center gap-2">
              <span class="w-3 h-3 rounded-full bg-rose-400"></span> Pengeluaran per Kategori
            </h3>
            <div class="space-y-4">
              <div v-for="cat in financeData?.expense_by_category" :key="cat.category" class="flex justify-between items-center p-4 rounded-xl bg-slate-900/50 border border-white/5 hover:border-rose-500/20 transition-colors">
                <span class="text-slate-300">{{ cat.category }}</span>
                <span class="font-bold text-rose-400">{{ formatCurrency(cat.total) }}</span>
              </div>
              <p v-if="!financeData?.expense_by_category?.length" class="text-slate-500 text-center py-6">Tidak ada data pengeluaran</p>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>