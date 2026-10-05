<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import DataTable from "@/Components/ui/table/DataTable.vue"
import Card from "@/Components/ui/card/Card.vue"
import Button from "@/Components/ui/button/Button.vue"
import MobileFab from "@/Components/MobileFab.vue"
import Input from "@/Components/ui/input/Input.vue"
import Select from "@/Components/ui/select/Select.vue"
import Badge from "@/Components/ui/badge/Badge.vue"
import { Head, Link, useForm } from "@inertiajs/vue3"

const props = defineProps({ distributions: Object, slaughterings: Array })


import Dialog from "@/Components/ui/dialog/Dialog.vue"
const mobileFormOpen = ref(false)
const submitMobile = () => {
    submit()
    mobileFormOpen.value = false
}

const form = useForm({
  recipient_name: "",
  recipient_type: "mustahik",
  slaughtering_id: "",
  package_count: 1,
})
const submit = () => form.post(route("distributions.store"))

const statusVariant = (status) => (status === "delivered" ? "success" : "warning")
const distStatusVariant = (s) => s === "completed" ? "success" : s === "in_progress" ? "warning" : "default"

const slaughteringLabel = (s) => {
  const animal = s.animal
  return `#${s.id} — ${s.date} — ${animal ? `${animal.type} #${animal.id} (${animal.weight}kg)` : "?"}`
}
</script>

<template>
  <Head title="Distribusi Daging" />
  <AuthenticatedLayout title="Distribusi Daging">

    <div class="space-y-6">
      <Card class="hidden md:block p-5">
        <p class="text-sm font-medium text-slate-700">Tambah Data Distribusi</p>
        <div class="mt-4 grid gap-3 md:grid-cols-5">
          <Select v-model="form.slaughtering_id">
            <option value="">— Pilih Penyembelihan —</option>
            <option v-for="s in slaughterings" :key="s.id" :value="s.id">{{ slaughteringLabel(s) }}</option>
          </Select>
          <Input v-model="form.recipient_name" placeholder="Nama Penerima" />
          <Select v-model="form.recipient_type">
            <option value="mustahik">mustahik</option>
            <option value="penerima">penerima</option>
          </Select>
          <Input v-model="form.package_count" type="number" min="1" />
          <Button @click="submit">Simpan</Button>
        </div>
      </Card>

      <DataTable v-if="distributions.data.length" :data="distributions.data">
        <template #mobile-card="{ item }">
          <div class="group relative flex flex-col justify-between rounded-3xl border border-emerald-900/5 bg-white shadow-lg shadow-emerald-900/5 ring-1 ring-slate-100/50 mb-3 p-4 transition-all active:scale-95">
            <div class="flex items-center justify-between">
              <div>
                <p class="font-bold text-slate-800 text-base leading-tight truncate">{{ item.recipient_name }}</p>
                <p class="text-xs text-slate-500 font-medium mt-1 truncate">{{ item.recipient_type }} • {{ item.package_count }} Paket</p>
              </div>
              <div class="text-right">
                <StatusBadge size="sm" :status="item.status" />
                <ActionMenu class="mt-1" :items="[{ key: 'delete', label: 'Hapus', tone: 'danger' }]" @select="deleteItem(item.id)" />
              </div>
            </div>
            <div v-if="item.status === 'pending'" class="mt-3">
               <Button variant="primary" class="w-full justify-center bg-emerald-600 hover:bg-emerald-700" @click="markDelivered(item.id)">
                  Tandai Selesai (Disalurkan)
               </Button>
            </div>
          </div>
        </template>
        <thead class="bg-slate-50 text-xs uppercase text-slate-500">
          <tr>
            <th class="px-4 py-3 text-left">Penerima</th>
            <th class="px-4 py-3 text-left">Penyembelihan</th>
            <th class="px-4 py-3 text-left">Jenis</th>
            <th class="px-4 py-3 text-left">Jumlah</th>
            <th class="px-4 py-3 text-left">Status</th>
            <th class="px-4 py-3 text-left"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in distributions.data" :key="item.id" class="border-t border-slate-100 hover:bg-slate-50/80">
            <td class="px-4 py-3">
              <p class="font-medium text-slate-800">{{ item.recipient_name }}</p>
            </td>
            <td class="px-4 py-3">
              <template v-if="item.slaughtering">
                <Badge size="sm">#{{ item.slaughtering.id }}</Badge>
                <p class="mt-1 text-xs text-slate-600">{{ item.slaughtering.date }}</p>
                <p class="text-xs text-slate-500">{{ item.slaughtering.animal?.type }} · {{ item.slaughtering.animal?.weight }}kg</p>
                <Badge size="sm" :variant="distStatusVariant(item.slaughtering.distribution_status)">{{ item.slaughtering.distribution_status }}</Badge>
              </template>
              <span v-else class="text-xs text-slate-400">—</span>
            </td>
            <td class="px-4 py-3">
              <Badge size="sm">{{ item.recipient_type }}</Badge>
            </td>
            <td class="px-4 py-3">{{ item.package_count }} paket</td>
            <td class="px-4 py-3">
              <Badge :variant="statusVariant(item.status)">{{ item.status }}</Badge>
            </td>
            <td class="px-4 py-3">
              <Link :href="route('distributions.show', item.id)">
                <Button size="sm" variant="outline">Detail</Button>
              </Link>
            </td>
          </tr>
        </tbody>
      </DataTable>
    </div>
  
    <!-- Mobile Floating Form Dialog (replaces inline form on mobile) -->
    <MobileFab @click="mobileFormOpen = true" />
    
    <Dialog :open="mobileFormOpen" @close="mobileFormOpen = false">
      <div class="p-5">
        <h3 class="text-lg font-bold text-slate-900 mb-4">Bagikan Daging</h3>
        <div class="space-y-4">
          <Select v-model="form.slaughtering_id" class="w-full">
            <option value="">— Pilih Penyembelihan —</option>
            <option v-for="s in slaughterings" :key="s.id" :value="s.id">{{ slaughteringLabel(s) }}</option>
          </Select>
          <Input v-model="form.recipient_name" placeholder="Nama Penerima" class="w-full h-12" />
          <Select v-model="form.recipient_type" class="w-full h-12">
            <option value="mustahik">Mustahik</option>
            <option value="penerima">Penerima Umum</option>
          </Select>
          <Input v-model="form.package_count" type="number" min="1" placeholder="Jumlah Paket" class="w-full h-12" />
          <Button @click="submitMobile" class="w-full h-12 bg-emerald-600">Simpan & Bagikan</Button>
        </div>
      </div>
    </Dialog>
  </AuthenticatedLayout>
</template>
