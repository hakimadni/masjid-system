<script setup>
import { Head, Link } from "@inertiajs/vue3"

const props = defineProps({
  mosque: { type: Object, default: null },
  prayerSchedules: { type: Array, default: () => [] },
  serviceSchedules: { type: Array, default: () => [] },
})

const daysOfWeek = ['Ahad', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']
</script>

<template>
  <Head title="Jadwal Shalat & Kegiatan" />

  <div class="min-h-screen bg-slate-50">
    <header class="bg-white border-b border-slate-200">
      <div class="container mx-auto px-4 py-4">
        <nav class="flex gap-4 items-center">
          <Link href="/" class="text-sm font-medium text-slate-600 hover:text-emerald-600">← Beranda</Link>
          <h1 class="text-lg font-semibold text-slate-900">Jadwal Shalat & Kegiatan</h1>
        </nav>
      </div>
    </header>

    <main class="container mx-auto px-4 py-8 space-y-8">
      <div class="bg-white rounded-xl border border-slate-200 p-6">
        <h2 class="text-xl font-semibold text-slate-900 mb-4">Jadwal Shalat</h2>
        <p class="text-slate-600 mb-4">Jadwal shalat akan ditampilkan melalui integrasi API eksternal.</p>
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="border-b">
                <th class="text-left py-2">Hari</th>
                <th class="text-left py-2">Subuh</th>
                <th class="text-left py-2">Dzuhur</th>
                <th class="text-left py-2">Ashar</th>
                <th class="text-left py-2">Maghrib</th>
                <th class="text-left py-2">Isya</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(schedule, index) in prayerSchedules" :key="index" class="border-b last:border-0">
                <td class="py-2 font-medium">{{ schedule.day || daysOfWeek[index] }}</td>
                <td class="py-2">{{ schedule.fajr || '-' }}</td>
                <td class="py-2">{{ schedule.dzuhur || '-' }}</td>
                <td class="py-2">{{ schedule.ashar || '-' }}</td>
                <td class="py-2">{{ schedule.maghrib || '-' }}</td>
                <td class="py-2">{{ schedule.isya || '-' }}</td>
              </tr>
              <tr v-if="!prayerSchedules.length">
                <td colspan="6" class="py-8 text-center text-slate-500">Belum ada jadwal shalat yang dikonfigurasi</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="bg-white rounded-xl border border-slate-200 p-6">
        <h2 class="text-xl font-semibold text-slate-900 mb-4">Jadwal Kegiatan</h2>
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="border-b">
                <th class="text-left py-2">Hari</th>
                <th class="text-left py-2">Waktu</th>
                <th class="text-left py-2">Kegiatan</th>
                <th class="text-left py-2">Tempat</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="schedule in serviceSchedules" :key="schedule.id" class="border-b last:border-0">
                <td class="py-2 font-medium">{{ schedule.day_of_week || '-' }}</td>
                <td class="py-2">{{ schedule.time || '-' }}</td>
                <td class="py-2">{{ schedule.title || '-' }}</td>
                <td class="py-2">{{ schedule.location || '-' }}</td>
              </tr>
              <tr v-if="!serviceSchedules.length">
                <td colspan="4" class="py-8 text-center text-slate-500">Belum ada jadwal kegiatan</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>
</template>