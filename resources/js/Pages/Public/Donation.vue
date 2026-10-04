<script setup>
import { Head, Link } from "@inertiajs/vue3"
import { ref, computed } from "vue"

const props = defineProps({
  mosque: { type: Object, default: null },
  settings: { type: Object, default: () => ({}) },
})

const formatCurrency = (value) =>
  new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    maximumFractionDigits: 0,
  }).format(Number(value || 0))

const copiedState = ref(false)
const copyToClipboard = async (text) => {
  try {
    await navigator.clipboard.writeText(text)
    copiedState.value = true
    setTimeout(() => {
      copiedState.value = false
    }, 2000)
  } catch (err) {
    console.error('Failed to copy!', err)
  }
}
</script>

<template>
  <Head :title="'Donasi - ' + (mosque?.name || 'MasjidOS')" />

  <div class="min-h-screen bg-slate-900 font-sans selection:bg-emerald-500 selection:text-white pb-20">
    <!-- Navbar (Glassmorphism) -->
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

    <main class="container mx-auto px-6 pt-32 max-w-3xl">
      <!-- Header Section -->
      <div class="text-center mb-12 relative">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-64 h-64 bg-emerald-500/20 rounded-full blur-[80px] -z-10 pointer-events-none"></div>
        <h1 class="text-4xl md:text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-emerald-300 to-teal-500 mb-4">Salurkan Sedekah Anda</h1>
        <p class="text-lg text-slate-400 font-light max-w-xl mx-auto">
          "Perumpamaan orang-orang yang menafkahkan hartanya di jalan Allah adalah serupa dengan sebutir benih yang menumbuhkan tujuh bulir..." (QS. Al-Baqarah: 261)
        </p>
      </div>

      <div class="space-y-8">
        <!-- Bank Transfer Card -->
        <div class="bg-slate-800/40 backdrop-blur-md border border-white/5 rounded-3xl p-8 relative overflow-hidden shadow-2xl">
          <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-blue-500/10 rounded-full blur-[40px] pointer-events-none"></div>
          
          <div class="flex items-center gap-4 mb-8">
            <div class="w-12 h-12 bg-emerald-500/20 rounded-xl flex items-center justify-center">
              <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
            </div>
            <h2 class="text-2xl font-bold text-white">Transfer Bank Resmi</h2>
          </div>

          <div class="space-y-6 relative z-10">
            <div class="bg-slate-900/50 rounded-2xl p-6 border border-white/5">
              <p class="text-sm font-medium text-slate-400 mb-2 uppercase tracking-wider">Bank Tujuan</p>
              <p class="text-xl text-white font-semibold">{{ settings.bank_accounts?.[0]?.bank_name || 'BSI (Bank Syariah Indonesia)' }}</p>
            </div>

            <div class="bg-slate-900/50 rounded-2xl p-6 border border-white/5 flex items-center justify-between group">
              <div>
                <p class="text-sm font-medium text-slate-400 mb-2 uppercase tracking-wider">No. Rekening</p>
                <p class="text-2xl md:text-3xl font-mono text-emerald-400 font-bold tracking-widest">{{ settings.bank_accounts?.[0]?.account_number || '714-233-1990' }}</p>
              </div>
              <button @click="copyToClipboard(settings.bank_accounts?.[0]?.account_number || '7142331990')" class="bg-slate-800 hover:bg-slate-700 text-white p-3 rounded-xl transition-all" title="Salin Rekening">
                <svg v-if="!copiedState" class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                <svg v-else class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
              </button>
            </div>

            <div class="bg-slate-900/50 rounded-2xl p-6 border border-white/5">
              <p class="text-sm font-medium text-slate-400 mb-2 uppercase tracking-wider">Atas Nama</p>
              <p class="text-xl text-white font-semibold">{{ settings.bank_accounts?.[0]?.account_holder || 'Yayasan Masjid Kita' }}</p>
            </div>
          </div>
        </div>

        <!-- QRIS Card (Optional) -->
        <div v-if="settings.qris_info?.image_url" class="bg-slate-800/40 backdrop-blur-md border border-white/5 rounded-3xl p-8 text-center">
          <p class="text-sm font-medium text-slate-400 mb-4 uppercase tracking-wider">Scan QRIS</p>
          <div class="inline-block bg-white p-4 rounded-2xl">
            <img :src="settings.qris_info.image_url" alt="QRIS" class="w-48 h-48 md:w-64 md:h-64 object-contain" />
          </div>
        </div>

        <!-- Info / Steps Card -->
        <div class="bg-gradient-to-br from-emerald-900 to-slate-900 border border-emerald-500/20 rounded-3xl p-8">
          <h3 class="font-bold text-white mb-6 text-xl">Langkah Konfirmasi</h3>
          <ul class="space-y-4 text-slate-300 font-light">
            <li class="flex items-start gap-4">
              <div class="w-8 h-8 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 font-bold">1</div>
              <p class="pt-1">Lakukan transfer ke rekening di atas sesuai dengan niat Anda.</p>
            </li>
            <li class="flex items-start gap-4">
              <div class="w-8 h-8 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 font-bold">2</div>
              <p class="pt-1">Sertakan keterangan <strong class="text-white font-medium">"Sedekah/Infaq"</strong> pada berita transfer.</p>
            </li>
            <li class="flex items-start gap-4">
              <div class="w-8 h-8 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 font-bold">3</div>
              <div>
                <p class="pt-1 mb-2">Simpan bukti transfer dan hubungi narahubung kami untuk pencatatan otomatis di sistem.</p>
                <a v-if="mosque?.phone" :href="'https://wa.me/' + mosque.phone.replace(/[^0-9]/g, '')" target="_blank" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                  Konfirmasi WhatsApp
                </a>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </main>
  </div>
</template>