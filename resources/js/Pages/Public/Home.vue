<script setup>
import { Head, Link } from "@inertiajs/vue3"
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

const props = defineProps({
  mosque: { type: Object, required: true },
  todayPrayerTimes: { type: Object, default: null },
  upcomingEvents: { type: Array, default: () => [] },
  latestAnnouncements: { type: Array, default: () => [] },
  financeSummary: { type: Object, default: () => ({}) },
  settings: { type: Object, default: () => ({}) },
})

const formatCurrency = (value) =>
  new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    maximumFractionDigits: 0,
  }).format(Number(value || 0))
</script>

<template>
  <Head :title="'Beranda - ' + (mosque?.name || 'MasjidOS')" />

  <div class="min-h-screen bg-slate-900 font-sans selection:bg-emerald-500 selection:text-white">
    <!-- Navbar (Glassmorphism) -->
    <header class="fixed top-0 left-0 right-0 z-50 bg-slate-900/80 backdrop-blur-md border-b border-white/10 transition-all duration-300">
      <div class="container mx-auto px-6 py-4 flex justify-between items-center">
        <div class="flex items-center gap-3 group cursor-pointer">
          <div class="w-10 h-10 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/30 group-hover:scale-105 transition-transform">
            <ApplicationLogo class="w-6 h-6 text-white" />
          </div>
          <div>
            <h1 class="font-bold text-white tracking-wide">{{ mosque?.name || 'MasjidOS' }}</h1>
            <p class="text-xs text-emerald-400 font-medium tracking-wider uppercase">{{ mosque?.address || 'Sistem Manajemen Masjid' }}</p>
          </div>
        </div>
        <nav class="hidden md:flex gap-8">
          <Link href="/" class="text-sm font-semibold text-white transition-colors">Beranda</Link>
          <Link href="/profil" class="text-sm font-medium text-slate-300 hover:text-emerald-400 transition-colors">Profil</Link>
          <Link href="/jadwal" class="text-sm font-medium text-slate-300 hover:text-emerald-400 transition-colors">Jadwal</Link>
          <Link href="/kajian" class="text-sm font-medium text-slate-300 hover:text-emerald-400 transition-colors">Kajian</Link>
          <Link href="/donasi" class="text-sm font-medium text-slate-300 hover:text-emerald-400 transition-colors">Donasi</Link>
          <Link v-if="settings?.show_public_report" href="/laporan-keuangan" class="text-sm font-medium text-slate-300 hover:text-emerald-400 transition-colors">Laporan Keuangan</Link>
          <Link href="/kontak" class="text-sm font-medium text-slate-300 hover:text-emerald-400 transition-colors">Kontak</Link>
        </nav>
      </div>
    </header>

    <main class="pt-24 pb-16 space-y-24">
      <!-- Hero Section -->
      <section class="relative container mx-auto px-6 py-20 text-center">
        <div class="absolute inset-0 -z-10 flex items-center justify-center pointer-events-none">
          <div class="w-96 h-96 bg-emerald-500/20 rounded-full blur-[120px]"></div>
          <div class="w-96 h-96 bg-blue-500/10 rounded-full blur-[120px] -ml-20 mt-20"></div>
        </div>
        <h2 class="text-5xl md:text-7xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-emerald-300 to-teal-500 mb-6 tracking-tight">
          Selamat Datang di <br class="hidden md:block"/> {{ mosque?.name || 'Masjid Kami' }}
        </h2>
        <p class="text-lg md:text-xl text-slate-300 max-w-2xl mx-auto mb-10 leading-relaxed font-light">
          {{ mosque?.description || 'Pusat ibadah, ilmu, dan pemberdayaan umat. Transparan dalam keuangan, amanah dalam pelayanan.' }}
        </p>
        <div class="flex justify-center gap-4">
          <Link v-if="settings?.show_donation_page" href="/donasi" class="bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white font-semibold py-3 px-8 rounded-full shadow-lg shadow-emerald-500/25 transition-all hover:-translate-y-0.5">
            Mulai Donasi
          </Link>
          <Link href="/jadwal" class="bg-slate-800 hover:bg-slate-700 text-white border border-slate-700 font-semibold py-3 px-8 rounded-full transition-all hover:-translate-y-0.5">
            Lihat Jadwal
          </Link>
        </div>
      </section>

      <!-- Prayer Times (Glassmorphism Cards) -->
      <section v-if="todayPrayerTimes && settings?.show_prayer_schedule" class="container mx-auto px-6">
        <div class="bg-slate-800/50 backdrop-blur-xl border border-white/10 rounded-3xl p-8 shadow-2xl relative overflow-hidden">
          <div class="absolute top-0 right-0 w-64 h-64 bg-emerald-500/10 rounded-full blur-[80px]"></div>
          <h3 class="text-2xl font-bold text-white mb-8 text-center flex items-center justify-center gap-3">
            <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Waktu Shalat Hari Ini
          </h3>
          <div class="grid grid-cols-2 md:grid-cols-7 gap-4 relative z-10">
            <div v-for="(time, name) in todayPrayerTimes" :key="name" class="group bg-slate-900/50 hover:bg-emerald-500/10 border border-white/5 hover:border-emerald-500/30 transition-all duration-300 rounded-2xl p-5 text-center flex flex-col items-center justify-center transform hover:-translate-y-1">
              <p class="text-sm font-medium text-slate-400 capitalize mb-1">{{ name.replace('_', ' ') }}</p>
              <p class="text-2xl font-bold text-white group-hover:text-emerald-400 transition-colors">{{ time }}</p>
            </div>
          </div>
        </div>
      </section>

      <!-- Dynamic Split Content: Events & Finance -->
      <section class="container mx-auto px-6">
        <div class="grid lg:grid-cols-2 gap-8">
          
          <!-- Upcoming Events -->
          <div v-if="upcomingEvents.length && settings?.show_events" class="bg-slate-800/40 backdrop-blur-md border border-white/5 rounded-3xl p-8 hover:bg-slate-800/60 transition-colors">
            <div class="flex justify-between items-end mb-8">
              <div>
                <h3 class="text-2xl font-bold text-white">Kajian & Event</h3>
                <p class="text-slate-400 mt-1">Agenda terdekat di masjid</p>
              </div>
              <Link href="/kajian" class="text-sm font-medium text-emerald-400 hover:text-emerald-300">Semua Event &rarr;</Link>
            </div>
            <div class="space-y-4">
              <div v-for="event in upcomingEvents" :key="event.id" class="flex gap-4 p-4 rounded-2xl bg-slate-900/50 border border-transparent hover:border-white/10 transition-colors cursor-default">
                <div class="w-12 h-12 rounded-xl bg-emerald-500/20 flex flex-col items-center justify-center shrink-0">
                  <span class="text-emerald-400 font-bold leading-none">{{ event.start_at.substring(8, 10) }}</span>
                  <span class="text-[10px] text-emerald-400/80 uppercase font-semibold">{{ new Date(event.start_at).toLocaleString('id-ID', { month: 'short' }) }}</span>
                </div>
                <div>
                  <p class="font-semibold text-white text-lg leading-tight">{{ event.title }}</p>
                  <p class="text-sm text-slate-400 mt-1 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ event.start_at.substring(11, 16) }} WIB
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Public Finance Summary -->
          <div v-if="settings?.show_public_report" class="bg-slate-800/40 backdrop-blur-md border border-white/5 rounded-3xl p-8 hover:bg-slate-800/60 transition-colors relative overflow-hidden">
            <div class="absolute -bottom-24 -right-24 w-64 h-64 bg-emerald-500/10 rounded-full blur-[60px]"></div>
            <div class="flex justify-between items-end mb-8 relative z-10">
              <div>
                <h3 class="text-2xl font-bold text-white">Transparansi Dana</h3>
                <p class="text-slate-400 mt-1">Laporan kas masjid terkini</p>
              </div>
              <Link href="/laporan-keuangan" class="text-sm font-medium text-emerald-400 hover:text-emerald-300">Rincian &rarr;</Link>
            </div>
            <div class="space-y-4 relative z-10">
              <div class="bg-slate-900/50 rounded-2xl p-6 border border-white/5">
                <p class="text-sm text-slate-400 mb-1">Total Saldo Kas</p>
                <p class="text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-emerald-300 to-teal-500">{{ formatCurrency(financeSummary.balance) }}</p>
              </div>
              <div class="grid grid-cols-2 gap-4">
                <div class="bg-slate-900/50 rounded-2xl p-5 border border-emerald-500/20 relative overflow-hidden group">
                  <div class="absolute inset-0 bg-emerald-500/5 translate-y-full group-hover:translate-y-0 transition-transform"></div>
                  <p class="text-sm text-slate-400 mb-1">Pemasukan</p>
                  <p class="text-xl font-bold text-emerald-400">{{ formatCurrency(financeSummary.income_total) }}</p>
                </div>
                <div class="bg-slate-900/50 rounded-2xl p-5 border border-rose-500/20 relative overflow-hidden group">
                  <div class="absolute inset-0 bg-rose-500/5 translate-y-full group-hover:translate-y-0 transition-transform"></div>
                  <p class="text-sm text-slate-400 mb-1">Pengeluaran</p>
                  <p class="text-xl font-bold text-rose-400">{{ formatCurrency(financeSummary.expense_total) }}</p>
                </div>
              </div>
            </div>
          </div>

        </div>
      </section>

      <!-- Latest Announcements -->
      <section v-if="latestAnnouncements.length && settings?.show_announcements" class="container mx-auto px-6">
        <div class="text-center mb-10">
          <h3 class="text-3xl font-bold text-white">Pengumuman Terbaru</h3>
        </div>
        <div class="grid md:grid-cols-3 gap-6">
          <div v-for="announcement in latestAnnouncements" :key="announcement.id" class="bg-slate-800/40 backdrop-blur-sm border border-white/5 rounded-2xl p-6 group hover:border-emerald-500/30 transition-colors">
            <div class="w-10 h-10 rounded-full bg-blue-500/20 flex items-center justify-center mb-4 text-blue-400 group-hover:scale-110 transition-transform">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
            </div>
            <p class="font-bold text-lg text-white mb-2">{{ announcement.title }}</p>
            <p class="text-sm text-slate-400 leading-relaxed line-clamp-3">{{ announcement.excerpt }}</p>
          </div>
        </div>
      </section>

      <!-- Contact CTA -->
      <section v-if="settings?.show_contact" class="container mx-auto px-6">
        <div class="bg-gradient-to-br from-emerald-900 to-slate-900 border border-emerald-500/20 rounded-3xl p-10 md:p-16 text-center relative overflow-hidden">
          <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-emerald-500/10 rounded-full blur-[100px] -translate-y-1/2 translate-x-1/3"></div>
          <h3 class="text-3xl md:text-4xl font-bold text-white mb-4 relative z-10">Hubungi Pengurus Masjid</h3>
          <p class="text-emerald-100/70 mb-8 max-w-xl mx-auto relative z-10">
            Kami terbuka untuk pertanyaan, kerja sama kegiatan, dan layanan keumatan.
          </p>
          <div class="flex flex-wrap justify-center gap-6 relative z-10">
            <a v-if="mosque?.phone" :href="'tel:' + mosque.phone" class="flex items-center gap-2 bg-slate-800/80 hover:bg-slate-700 backdrop-blur-sm text-white px-6 py-3 rounded-xl border border-white/10 transition-all hover:-translate-y-1">
              <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
              {{ mosque.phone }}
            </a>
            <a v-if="mosque?.email" :href="'mailto:' + mosque.email" class="flex items-center gap-2 bg-slate-800/80 hover:bg-slate-700 backdrop-blur-sm text-white px-6 py-3 rounded-xl border border-white/10 transition-all hover:-translate-y-1">
              <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
              {{ mosque.email }}
            </a>
          </div>
        </div>
      </section>
    </main>

    <!-- Footer -->
    <footer class="bg-slate-950 border-t border-white/5 py-8 mt-auto">
      <div class="container mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-4 text-sm text-slate-500">
        <p>&copy; {{ new Date().getFullYear() }} {{ mosque?.name || 'MasjidOS' }}. Hak Cipta Dilindungi.</p>
        <p class="flex items-center gap-1">Ditenagai oleh <span class="font-semibold text-emerald-500">MasjidOS</span></p>
      </div>
    </footer>
  </div>
</template>