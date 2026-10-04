<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { 
  BuildingLibraryIcon, 
  BanknotesIcon, 
  HeartIcon, 
  UsersIcon, 
  CalendarDaysIcon, 
  DevicePhoneMobileIcon,
  ShieldCheckIcon,
  ClipboardDocumentCheckIcon,
  ChartPieIcon
} from '@heroicons/vue/24/outline';

defineProps({
    canLogin: {
        type: Boolean,
    },
});

const features = [
  {
    name: 'Manajemen Keuangan Transparan',
    description: 'Pencatatan kas masuk & keluar, sistem persetujuan berlapis, dan laporan keuangan terpusat yang otomatis direkap per bulan.',
    icon: BanknotesIcon,
  },
  {
    name: 'Sistem ZISWAF Terintegrasi',
    description: 'Kelola penerimaan Zakat, Infaq, Shadaqah, dan Wakaf dengan mudah. Sistem akan mencatat donatur dan menyalurkan ke mustahik secara akurat.',
    icon: HeartIcon,
  },
  {
    name: 'Administrasi Qurban Profesional',
    description: 'Mulai dari tabungan hewan qurban, pendaftaran peserta, jadwal penyembelihan, hingga distribusi daging ke masyarakat sekitar.',
    icon: BuildingLibraryIcon,
  },
  {
    name: 'Hak Akses RBAC (10+ Roles)',
    description: 'Keamanan ekstra dengan pemisahan akses untuk Ketua DKM, Bendahara, Sekretaris, Marbot, Panitia, hingga Donatur.',
    icon: ShieldCheckIcon,
  },
  {
    name: 'Manajemen Acara & Kajian',
    description: 'Atur jadwal pengajian bulanan, tablig akbar, dan acara masjid dengan publikasi langsung ke portal jamaah.',
    icon: CalendarDaysIcon,
  },
  {
    name: 'TV Display Interaktif (Digital Signage)',
    description: 'Siarkan waktu sholat realtime dari API MyQuran, video kajian YouTube, dan ringkasan kas Masjid langsung di layar TV.',
    icon: DevicePhoneMobileIcon,
  },
  {
    name: 'Pencatatan Aset & Inventaris',
    description: 'Pantau barang inventaris masjid, kondisi fisik aset, serta jadwal perawatannya agar fasilitas selalu prima.',
    icon: ClipboardDocumentCheckIcon,
  },
  {
    name: 'Database Jamaah & Relawan',
    description: 'Himpun data warga sekitar, partisipan acara, dan kelola manajemen shift serta jadwal tugas marbot/relawan.',
    icon: UsersIcon,
  },
  {
    name: 'Dashboard Pelaporan Pintar',
    description: 'Pantau analitik, tren donasi bulanan, jumlah jamaah, dan kinerja keuangan hanya lewat satu layar dashboard pintar.',
    icon: ChartPieIcon,
  },
];
</script>

<template>
    <Head title="Tentang MasjidOS" />

    <div class="bg-slate-50 min-h-screen font-sans selection:bg-emerald-500 selection:text-white">
        <!-- Hero Section -->
        <div class="relative bg-slate-900 overflow-hidden">
            <div class="absolute inset-y-0 w-full h-full bg-[url('https://www.transparenttextures.com/patterns/arabesque.png')] opacity-10 mix-blend-overlay"></div>
            
            <header class="absolute inset-x-0 top-0 z-50">
                <nav class="flex items-center justify-between p-6 lg:px-8" aria-label="Global">
                    <div class="flex lg:flex-1 items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/30">
                            <ApplicationLogo class="w-6 h-6 text-white" />
                        </div>
                        <span class="font-bold text-white tracking-wide text-xl">MasjidOS</span>
                    </div>
                    <div class="flex flex-1 justify-end">
                        <Link v-if="canLogin" href="/login" class="text-sm font-semibold leading-6 text-white bg-white/10 px-4 py-2 rounded-lg hover:bg-white/20 transition-all backdrop-blur-sm">
                            Log in <span aria-hidden="true">&rarr;</span>
                        </Link>
                    </div>
                </nav>
            </header>

            <div class="relative isolate px-6 pt-14 lg:px-8">
                <div class="mx-auto max-w-4xl py-32 sm:py-48 lg:py-56">
                    <div class="text-center">
                        <div class="mb-6 flex justify-center">
                            <span class="relative rounded-full px-3 py-1 text-sm leading-6 text-emerald-300 ring-1 ring-white/10 hover:ring-white/20">
                                Sistem Informasi ERP Masjid Terlengkap.
                            </span>
                        </div>
                        <h1 class="text-4xl font-bold tracking-tight text-white sm:text-6xl">
                            Digitalisasi Manajemen <span class="text-emerald-400">Masjid Anda</span>
                        </h1>
                        <p class="mt-6 text-lg leading-8 text-slate-300 max-w-2xl mx-auto">
                            Tinggalkan pencatatan manual. MasjidOS membantu Dewan Kemakmuran Masjid (DKM) untuk mengelola keuangan, jamaah, ZISWAF, Qurban, hingga inventaris dalam satu platform cerdas nan transparan.
                        </p>
                        <div class="mt-10 flex items-center justify-center gap-x-6">
                            <Link href="/login" class="rounded-lg bg-emerald-500 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-emerald-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-400 transition-all">
                                Masuk ke Dashboard Utama
                            </Link>
                            <Link href="/" class="text-sm font-semibold leading-6 text-white flex items-center gap-2 hover:text-emerald-300 transition-colors">
                                Lihat Portal Jamaah <span aria-hidden="true">→</span>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div class="py-24 sm:py-32 bg-white">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-2xl lg:text-center">
                    <h2 class="text-base font-semibold leading-7 text-emerald-600">Terpadu & Komprehensif</h2>
                    <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Semua Fitur yang DKM Butuhkan</p>
                    <p class="mt-6 text-lg leading-8 text-slate-600">
                        Dari laporan transparansi kas untuk jamaah, hingga manajemen ribuan peserta tabungan hewan qurban. MasjidOS menangani kerumitan administrasi agar pengurus dapat fokus pada ibadah & pemberdayaan.
                    </p>
                </div>
                <div class="mx-auto mt-16 max-w-2xl sm:mt-20 lg:mt-24 lg:max-w-none">
                    <dl class="grid max-w-xl grid-cols-1 gap-x-8 gap-y-16 lg:max-w-none lg:grid-cols-3">
                        <div v-for="feature in features" :key="feature.name" class="flex flex-col bg-slate-50 rounded-2xl p-6 border border-slate-100 hover:shadow-lg transition-all hover:-translate-y-1">
                            <dt class="flex items-center gap-x-3 text-base font-semibold leading-7 text-slate-900">
                                <div class="h-10 w-10 flex items-center justify-center rounded-lg bg-emerald-100">
                                    <component :is="feature.icon" class="h-6 w-6 text-emerald-600" aria-hidden="true" />
                                </div>
                                {{ feature.name }}
                            </dt>
                            <dd class="mt-4 flex flex-auto flex-col text-base leading-7 text-slate-600">
                                <p class="flex-auto">{{ feature.description }}</p>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="bg-emerald-50">
            <div class="mx-auto max-w-7xl px-6 py-24 sm:py-32 lg:flex lg:items-center lg:justify-between lg:px-8">
                <h2 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">
                    <span class="block">Siap memodernisasi Masjid Anda?</span>
                    <span class="block text-emerald-600">Mulai gunakan MasjidOS hari ini.</span>
                </h2>
                <div class="mt-10 flex items-center gap-x-6 lg:mt-0 lg:flex-shrink-0">
                    <Link href="/login" class="rounded-lg bg-emerald-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600">
                        Masuk Sistem
                    </Link>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <footer class="bg-white border-t border-slate-200">
            <div class="mx-auto max-w-7xl px-6 py-8 md:flex md:items-center md:justify-between lg:px-8">
                <div class="mt-8 md:order-1 md:mt-0">
                    <p class="text-center text-xs leading-5 text-slate-500">&copy; {{ new Date().getFullYear() }} MasjidOS MVP - Sistem ERP Masjid.</p>
                </div>
            </div>
        </footer>
    </div>
</template>
