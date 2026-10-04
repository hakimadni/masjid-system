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
          <table class="w-full text-left text-sm">
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
