<script setup>
import { Head, Link } from "@inertiajs/vue3"

const props = defineProps({
  mosque: { type: Object, default: null },
  show_contact: { type: Boolean, default: true },
})
</script>

<template>
  <Head title="Kontak" />

  <div class="min-h-screen bg-slate-50">
    <header class="bg-white border-b border-slate-200">
      <div class="container mx-auto px-4 py-4">
        <nav class="flex gap-4 items-center">
          <Link href="/" class="text-sm font-medium text-slate-600 hover:text-emerald-600">← Beranda</Link>
          <h1 class="text-lg font-semibold text-slate-900">Kontak</h1>
        </nav>
      </div>
    </header>

    <main class="container mx-auto px-4 py-8 max-w-2xl">
      <div class="bg-white rounded-xl border border-slate-200 p-6">
        <h2 class="text-xl font-semibold text-slate-900 mb-4">{{ mosque?.name || 'Masjid Kami' }}</h2>

        <div v-if="show_contact" class="space-y-4">
          <div>
            <p class="text-sm font-medium text-slate-500">Alamat</p>
            <p class="text-slate-900">{{ mosque?.address || 'Belum diisi' }}</p>
          </div>

          <div v-if="mosque?.phone">
            <p class="text-sm font-medium text-slate-500">Telepon</p>
            <p class="text-slate-900">{{ mosque.phone }}</p>
          </div>

          <div v-if="mosque?.email">
            <p class="text-sm font-medium text-slate-500">Email</p>
            <p class="text-slate-900">{{ mosque.email }}</p>
          </div>

          <div v-if="mosque?.city_id">
            <p class="text-sm font-medium text-slate-500">Kota</p>
            <p class="text-slate-900">{{ mosque.city_id }}</p>
          </div>
        </div>

        <p v-else class="text-slate-500">Informasi kontak belum ditampilkan secara publik.</p>

        <div class="mt-8 pt-6 border-t">
          <p class="text-xs text-slate-500">
            Untuk informasi lebih lanjut atau koordinasi kegiatan, silakan hubungi pengurus masjid.
          </p>
        </div>
      </div>
    </main>
  </div>
</template>