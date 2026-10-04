<script setup>
import { Link } from "@inertiajs/vue3"
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import AnimalStatusBadge from "@/Components/qurban/AnimalStatusBadge.vue"

defineProps({ animals: Object })
</script>

<template>
<AuthenticatedLayout title="Hewan">
  <template #header>
    <h2 class="font-semibold text-xl text-slate-800">Daftar Hewan</h2>
  </template>

  <div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
          
          <!-- Mobile List -->
          <div class="block md:hidden space-y-4 mb-6">
            <div v-for="animal in animals.data" :key="animal.id" class="group relative flex flex-col overflow-hidden rounded-3xl border border-emerald-900/5 bg-white shadow-lg shadow-emerald-900/5 ring-1 ring-slate-100/50 p-4 active:scale-95 transition-all">
              <div class="flex items-center justify-between">
                <div>
                  <p class="font-bold text-slate-800 text-base leading-tight">Hewan #{{ animal.id }} - <span class="capitalize">{{ animal.type }}</span></p>
                  <p class="text-xs text-slate-500 font-medium mt-1">{{ animal.weight }} kg • Rp {{ Number(animal.price).toLocaleString('id-ID') }}</p>
                </div>
                <div class="text-right">
                  <AnimalStatusBadge :status="animal.status" />
                </div>
              </div>
              <div class="mt-4 flex gap-2">
                <Link :href="route('animals.show', animal.id)" class="flex-1 inline-flex justify-center items-center px-4 py-2 bg-emerald-100 text-emerald-700 font-semibold text-sm rounded-xl hover:bg-emerald-200">
                  Lihat Detail
                </Link>
              </div>
            </div>
            
            <div v-if="!animals.data || animals.data.length === 0" class="text-center py-8 text-slate-500">
              Belum ada data hewan
            </div>
          </div>

          <table class="hidden md:table w-full text-left text-sm">
            <thead>
              <tr class="border-b-2 border-slate-200">
                <th class="px-4 py-3 font-semibold">No</th>
                <th class="px-4 py-3 font-semibold">Jenis</th>
                <th class="px-4 py-3 font-semibold">Berat</th>
                <th class="px-4 py-3 font-semibold">Harga</th>
                <th class="px-4 py-3 font-semibold">Supplier</th>
                <th class="px-4 py-3 font-semibold">Lokasi</th>
                <th class="px-4 py-3 font-semibold">Status</th>
                <th class="px-4 py-3 font-semibold">Peserta</th>
                <th class="px-4 py-3 font-semibold"></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(animal, idx) in animals.data" :key="animal.id" class="border-t hover:bg-slate-50">
                <td class="px-4 py-3">{{ idx + 1 + ((animals.current_page - 1) * animals.per_page) }}</td>
                <td class="px-4 py-3 capitalize">{{ animal.type }}</td>
                <td class="px-4 py-3">{{ animal.weight }} kg</td>
                <td class="px-4 py-3">Rp {{ Number(animal.price).toLocaleString('id-ID') }}</td>
                <td class="px-4 py-3">{{ animal.supplier }}</td>
                <td class="px-4 py-3">{{ animal.location }}</td>
                <td class="px-4 py-3"><AnimalStatusBadge :status="animal.status" /></td>
                <td class="px-4 py-3">{{ animal.participants_count }}/7</td>
                <td class="px-4 py-3">
                  <Link :href="route('animals.show', animal.id)" class="inline-block px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">
                    Detail
                  </Link>
                </td>
              </tr>
              <tr v-if="!animals.data || animals.data.length === 0">
                <td colspan="9" class="px-4 py-8 text-center text-slate-400">Belum ada data hewan</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</AuthenticatedLayout>
</template>
