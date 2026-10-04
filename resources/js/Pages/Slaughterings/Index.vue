<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import AnimalStatusBadge from "@/Components/qurban/AnimalStatusBadge.vue"
import DataTable from "@/Components/ui/table/DataTable.vue"
import Card from "@/Components/ui/card/Card.vue"
import Button from "@/Components/ui/button/Button.vue"
import Input from "@/Components/ui/input/Input.vue"
import Select from "@/Components/ui/select/Select.vue"
import Badge from "@/Components/ui/badge/Badge.vue"
import { Head, Link, useForm } from "@inertiajs/vue3"

defineProps({ slaughterings: Object, animals: Array })

const form = useForm({ animal_id: "", date: "", location: "" })
const submit = () => form.post(route("slaughterings.store"))

const distStatusVariant = (s) => s === "completed" ? "success" : s === "in_progress" ? "warning" : "default"
const animalLabel = (a) => `#${a.id} — ${a.type} (${a.weight}kg) — ${a.supplier || "-"} [${a.status}]`
</script>

<template>
  <Head title="Penyembelihan" />
  <AuthenticatedLayout title="Penyembelihan">

    <div class="space-y-6">
      <Card class="p-5">
        <p class="text-sm font-medium text-slate-700">Jadwalkan Penyembelihan</p>
        <div class="mt-4 grid gap-3 md:grid-cols-4">
          <Select v-model="form.animal_id">
            <option disabled value="">Pilih Hewan</option>
            <option v-for="a in animals" :key="a.id" :value="a.id">{{ animalLabel(a) }}</option>
          </Select>
          <Input v-model="form.date" type="date" />
          <Input v-model="form.location" placeholder="Lokasi" />
          <Button @click="submit">Simpan</Button>
        </div>
      </Card>

      <DataTable>
        <thead class="bg-slate-50 text-xs uppercase text-slate-500">
          <tr>
            <th class="px-4 py-3 text-left">Hewan</th>
            <th class="px-4 py-3 text-left">Tanggal</th>
            <th class="px-4 py-3 text-left">Lokasi</th>
            <th class="px-4 py-3 text-left">Relawan</th>
            <th class="px-4 py-3 text-left">Distribusi</th>
            <th class="px-4 py-3 text-left">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in slaughterings.data" :key="item.id" class="border-t border-slate-100 hover:bg-slate-50/80">
            <td class="px-4 py-3">
              <template v-if="item.animal">
                <Badge>{{ item.animal.type }}</Badge>
                <p class="text-xs text-slate-600">{{ item.animal.supplier }}</p>
                <p class="text-xs text-slate-500">#{{ item.animal_id }} · {{ item.animal.weight }}kg</p>
                <p class="mt-1"><AnimalStatusBadge :status="item.animal.status" /></p>
              </template>
              <span v-else class="text-xs text-slate-400">#{{ item.animal_id }}</span>
            </td>
            <td class="px-4 py-3">{{ item.date }}</td>
            <td class="px-4 py-3">{{ item.location }}</td>
            <td class="px-4 py-3">
              <span class="font-medium">{{ item.volunteers_count ?? 0 }}</span>
            </td>
            <td class="px-4 py-3">
              <Badge size="sm" :variant="distStatusVariant(item.distribution_status)">{{ item.distribution_status }}</Badge>
            </td>
            <td class="px-4 py-3">
              <Link :href="route('slaughterings.show', item.id)">
                <Button size="sm" variant="outline">Detail</Button>
              </Link>
            </td>
          </tr>
        </tbody>
      </DataTable>
    </div>
  </AuthenticatedLayout>
</template>
