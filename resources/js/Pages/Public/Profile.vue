<script setup>
import { Head, Link } from "@inertiajs/vue3"

const props = defineProps({
  mosque: { type: Object, default: null },
})
</script>

<template>
  <Head title="Profil Masjid" />

  <div class="min-h-screen bg-slate-50">
    <!-- Header -->
    <header class="bg-white border-b border-slate-200">
      <div class="container mx-auto px-4 py-4">
        <nav class="flex gap-4 items-center">
          <Link href="/" class="text-sm font-medium text-slate-600 hover:text-emerald-600">← Beranda</Link>
          <h1 class="text-lg font-semibold text-slate-900">Profil Masjid</h1>
        </nav>
      </div>
    </header>

    <main class="container mx-auto px-4 py-8">
      <div class="bg-white rounded-xl border border-slate-200 p-8">
        <div class="flex items-start gap-6 mb-6">
          <div class="w-20 h-20 bg-emerald-600 rounded-lg flex items-center justify-center flex-shrink-0">
            <span class="text-white font-bold text-3xl">م</span>
          </div>
          <div>
            <h2 class="text-2xl font-bold text-slate-900">{{ mosque?.name || 'Nama Masjid' }}</h2>
            <p class="text-slate-600 mt-1">{{ mosque?.address || 'Alamat belum diisi' }}</p>
          </div>
        </div>

        <div v-if="mosque?.description" class="prose max-w-none">
          <h3 class="text-lg font-semibold text-slate-900 mb-3">Tentang Kami</h3>
          <p class="text-slate-600">{{ mosque.description }}</p>
        </div>

        <div class="grid md:grid-cols-2 gap-6 mt-6">
          <div v-if="mosque?.phone" class="flex items-center gap-3">
            <span class="text-emerald-600">📞</span>
            <span class="text-slate-700">{{ mosque.phone }}</span>
          </div>
          <div v-if="mosque?.email" class="flex items-center gap-3">
            <span class="text-emerald-600">✉️</span>
            <span class="text-slate-700">{{ mosque.email }}</span>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>