<script setup>
import { Head, Link } from "@inertiajs/vue3"

const props = defineProps({
  mosque: { type: Object, default: null },
  announcements: { type: Object, required: true },
})

const formatDate = (dateStr) => {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  })
}
</script>

<template>
  <Head title="Pengumuman" />

  <div class="min-h-screen bg-slate-50">
    <header class="bg-white border-b border-slate-200">
      <div class="container mx-auto px-4 py-4">
        <nav class="flex gap-4 items-center">
          <Link href="/" class="text-sm font-medium text-slate-600 hover:text-emerald-600">← Beranda</Link>
          <h1 class="text-lg font-semibold text-slate-900">Pengumuman</h1>
        </nav>
      </div>
    </header>

    <main class="container mx-auto px-4 py-8 max-w-3xl">
      <article v-for="announcement in announcements.data" :key="announcement.id" class="bg-white rounded-xl border border-slate-200 p-6 mb-4">
        <h2 class="text-xl font-semibold text-slate-900 mb-2">{{ announcement.title }}</h2>
        <p class="text-sm text-slate-500 mb-4">{{ formatDate(announcement.published_at) }}</p>
        <div class="prose text-slate-600" v-html="announcement.content || announcement.excerpt"></div>
      </article>

      <div v-if="!announcements.data.length" class="text-center py-12">
        <p class="text-slate-500">Belum ada pengumuman.</p>
      </div>

      <!-- Pagination -->
      <div v-if="announcements.links?.length > 3" class="mt-8 flex justify-center gap-2">
        <Link
          v-for="link in announcements.links"
          :key="link.url"
          :href="link.url || '#'"
          :class="[
            'px-3 py-1 rounded text-sm',
            link.active ? 'bg-emerald-600 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50',
          ]"
          v-html="link.label"
        />
      </div>
    </main>
  </div>
</template>