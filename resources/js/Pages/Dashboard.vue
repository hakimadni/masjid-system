<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import StatCard from "@/Components/qurban/StatCard.vue"
import Card from "@/Components/ui/card/Card.vue"
import Button from "@/Components/ui/button/Button.vue"
import Badge from "@/Components/ui/badge/Badge.vue"
import AnimalStatusBadge from "@/Components/qurban/AnimalStatusBadge.vue"
import { Head, Link } from "@inertiajs/vue3"

defineProps({
  analytics: { type: Object, required: true },
  recent_savings: { type: Array, default: () => [] },
  recent_animals: { type: Array, default: () => [] },
  recent_slaughterings: { type: Array, default: () => [] },
})

const formatCurrency = (v) => new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", maximumFractionDigits: 0 }).format(Number(v || 0))
const distStatusVariant = (s) => s === "completed" ? "success" : s === "in_progress" ? "warning" : "default"
</script>

<template>
  <Head title="Dashboard Qurban" />

  <AuthenticatedLayout>
    <template #header>
      Dashboard Qurban
    </template>

    <div class="space-y-6">
      <!-- Stats Row -->
      <div class="grid gap-4 sm:grid-cols-2 md:grid-cols-4">
        <StatCard title="Total Hewan" :value="analytics.total_animals" />
        <StatCard title="Total Peserta" :value="analytics.total_participants" />
        <StatCard title="Total Tabungan" :value="analytics.total_savings" />
        <StatCard title="Total Dana" :value="formatCurrency(analytics.total_collection)" />
      </div>

      <div class="grid gap-4 sm:grid-cols-2 md:grid-cols-4">
        <StatCard title="Progress Sembelih" :value="`${analytics.slaughter_progress}%`" />
        <StatCard title="Progress Distribusi" :value="`${analytics.distribution_progress}%`" />
        <StatCard title="Total Sembelih" :value="analytics.slaughtering_total" />
        <StatCard title="Total Daging" :value="`${analytics.total_meat_kg ?? 0} kg`" />
      </div>

      <!-- Quick Access -->
      <Card class="p-5">
        <p class="text-sm font-medium text-slate-700">Akses Cepat Modul</p>
        <div class="mt-4 flex flex-wrap gap-3">
          <Link :href="route('savings.index')"><Button variant="outline">Tabungan</Button></Link>
          <Link :href="route('animals.index')"><Button variant="outline">Hewan</Button></Link>
          <Link :href="route('participants.index')"><Button variant="outline">Peserta</Button></Link>
          <Link :href="route('slaughterings.index')"><Button variant="outline">Penyembelihan</Button></Link>
          <Link :href="route('volunteers.index')"><Button variant="outline">Relawan</Button></Link>
          <Link :href="route('distributions.index')"><Button variant="outline">Distribusi</Button></Link>
        </div>
      </Card>

      <!-- Recent Savings -->
      <Card class="p-5">
        <div class="flex items-center justify-between">
          <p class="text-sm font-medium text-slate-700">Tabungan Terbaru</p>
          <Link :href="route('savings.index')"><Button size="sm" variant="ghost">Lihat Semua</Button></Link>
        </div>
        <div v-if="recent_savings.length" class="mt-3 divide-y divide-slate-100">
          <div v-for="s in recent_savings" :key="s.id" class="flex items-center justify-between py-2">
            <div>
              <p class="text-sm font-medium text-slate-700">{{ s.user?.name ?? "User #" + s.user_id }}</p>
              <p class="text-xs text-slate-500">Target: {{ formatCurrency(s.target_amount) }}</p>
            </div>
            <div class="text-right">
              <p class="text-sm font-semibold text-slate-800">{{ formatCurrency(s.current_balance) }}</p>
              <Badge size="sm" :variant="s.status === 'active' ? 'info' : s.status === 'completed' ? 'success' : 'danger'">{{ s.status }}</Badge>
            </div>
          </div>
        </div>
        <p v-else class="mt-3 text-xs text-slate-400">Belum ada tabungan.</p>
      </Card>

      <!-- Two-column: Recent Animals + Slaughterings -->
      <div class="grid gap-6 md:grid-cols-2">
        <Card class="p-5">
          <div class="flex items-center justify-between">
            <p class="text-sm font-medium text-slate-700">Hewan Terbaru</p>
            <Link :href="route('animals.index')"><Button size="sm" variant="ghost">Lihat Semua</Button></Link>
          </div>
          <div v-if="recent_animals.length" class="mt-3 divide-y divide-slate-100">
            <div v-for="a in recent_animals" :key="a.id" class="flex items-center justify-between py-2">
              <div>
                <p class="text-sm font-medium text-slate-700">#{{ a.id }} — {{ a.type }}</p>
                <p class="text-xs text-slate-500">{{ a.weight }}kg · {{ a.supplier }}</p>
              </div>
              <div class="flex items-center gap-2">
                <span class="text-xs text-slate-500">{{ a.participants_count ?? 0 }} peserta</span>
                <AnimalStatusBadge :status="a.status" />
              </div>
            </div>
          </div>
          <p v-else class="mt-3 text-xs text-slate-400">Belum ada hewan.</p>
        </Card>

        <Card class="p-5">
          <div class="flex items-center justify-between">
            <p class="text-sm font-medium text-slate-700">Penyembelihan Terbaru</p>
            <Link :href="route('slaughterings.index')"><Button size="sm" variant="ghost">Lihat Semua</Button></Link>
          </div>
          <div v-if="recent_slaughterings.length" class="mt-3 divide-y divide-slate-100">
            <div v-for="s in recent_slaughterings" :key="s.id" class="flex items-center justify-between py-2">
              <div>
                <p class="text-sm font-medium text-slate-700">#{{ s.id }} — {{ s.animal?.type ?? "?" }}</p>
                <p class="text-xs text-slate-500">{{ s.date }} · {{ s.location }}</p>
              </div>
              <div class="flex items-center gap-2">
                <span class="text-xs text-slate-500">{{ s.volunteers_count ?? 0 }} relawan</span>
                <Badge size="sm" :variant="distStatusVariant(s.distribution_status)">{{ s.distribution_status }}</Badge>
              </div>
            </div>
          </div>
          <p v-else class="mt-3 text-xs text-slate-400">Belum ada penyembelihan.</p>
        </Card>
      </div>

      <!-- Distribution summary -->
      <Card class="p-5">
        <p class="text-sm font-medium text-slate-700">Ringkasan Distribusi</p>
        <div class="mt-3 grid gap-3 text-sm md:grid-cols-3">
          <p>Total Distribusi: <span class="font-semibold">{{ analytics.distribution_delivered + analytics.distribution_pending }}</span></p>
          <p>Terkirim: <span class="font-semibold text-green-600">{{ analytics.distribution_delivered }}</span></p>
          <p>Pending: <span class="font-semibold text-amber-600">{{ analytics.distribution_pending }}</span></p>
        </div>
      </Card>
    </div>
  </AuthenticatedLayout>
</template>
