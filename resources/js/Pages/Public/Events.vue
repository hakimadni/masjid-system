<script setup>
import { Head, Link } from "@inertiajs/vue3"

const props = defineProps({
  mosque: { type: Object, default: null },
  events: { type: Object, required: true },
})
</script>

<template>
  <Head title="Kajian & Event" />

  <div class="min-h-screen bg-slate-50">
    <header class="bg-white border-b border-slate-200">
      <div class="container mx-auto px-4 py-4">
        <nav class="flex gap-4 items-center">
          <Link href="/" class="text-sm font-medium text-slate-600 hover:text-emerald-600">← Beranda</Link>
          <h1 class="text-lg font-semibold text-slate-900">Kajian & Event</h1>
        </nav>
      </div>
    </header>

    <main class="container mx-auto px-4 py-8">
      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <article v-for="event in events.data" :key="event.id" class="bg-white rounded-xl border border-slate-200 p-6">
          <h2 class="text-lg font-semibold text-slate-900 mb-2">{{ event.title }}</h2>
          <p class="text-sm text-slate-500 mb-3">{{ event.start_at }}</p>
          <p class="text-slate-600 text-sm line-clamp-3">{{ event.description || event.excerpt }}</p>
          <div v-if="event.location" class="mt-3 text-xs text-slate-500">
            📍 {{ event.location }}
          </div>
        </article>
      </div>

      <div v-if="!events.data.length" class="text-center py-12">
        <p class="text-slate-500">Belum ada event atau kajian yang ditemukan.</p>
      </div>

      <!-- Pagination -->
      <div v-if="events.links && events.links.length > 3" class="mt-8 flex justify-center gap-2">
        <Link
          v-for="link in events.links"
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