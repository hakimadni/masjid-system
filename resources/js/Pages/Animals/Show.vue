<script setup>
import { ref } from "vue"
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import AnimalStatusBadge from "@/Components/qurban/AnimalStatusBadge.vue"
import PaymentBadge from "@/Components/qurban/PaymentBadge.vue"
import Card from "@/Components/ui/card/Card.vue"
import Button from "@/Components/ui/button/Button.vue"
import Input from "@/Components/ui/input/Input.vue"
import Select from "@/Components/ui/select/Select.vue"
import Badge from "@/Components/ui/badge/Badge.vue"
import { Head, Link, useForm } from "@inertiajs/vue3"

const props = defineProps({ animal: Object })

const editing = ref(false)
const form = useForm({
  type: props.animal.type,
  weight: props.animal.weight ?? "",
  price: props.animal.price ?? "",
  supplier: props.animal.supplier ?? "",
  location: props.animal.location ?? "",
  slaughter_type: props.animal.slaughter_type ?? "onsite",
  vendor: props.animal.vendor ?? "",
  vendor_cost: props.animal.vendor_cost ?? "",
  pickup_schedule: props.animal.pickup_schedule ?? "",
  status: props.animal.status ?? "available",
})
const submitEdit = () => form.put(route("animals.update", props.animal.id), { onSuccess: () => { editing.value = false } })
const destroy = () => { if (confirm("Hapus hewan ini?")) useForm({}).delete(route("animals.destroy", props.animal.id)) }

const formatCurrency = (v) => new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", maximumFractionDigits: 0 }).format(Number(v || 0))
</script>

<template>
  <Head :title="'Hewan #' + animal.id" />
  <AuthenticatedLayout :title="'Hewan #' + animal.id">

    <div class="mb-4 flex items-center gap-3">
      <Link :href="route('animals.index')"><Button variant="ghost">&larr; Kembali</Button></Link>
      <Button v-if="!editing" size="sm" @click="editing = true">Edit</Button>
      <Button v-else size="sm" variant="outline" @click="editing = false">Batal</Button>
      <Button size="sm" variant="danger" @click="destroy">Hapus</Button>
    </div>

    <!-- Detail View -->
    <Card v-if="!editing" class="p-6">
      <div class="mb-4 flex items-center gap-3">
        <Badge>{{ animal.type }}</Badge>
        <AnimalStatusBadge :status="animal.status" />
      </div>
      <div class="grid gap-4 text-sm md:grid-cols-3">
        <div><p class="text-slate-500">Berat</p><p class="font-medium">{{ animal.weight }} kg</p></div>
        <div><p class="text-slate-500">Harga</p><p class="font-medium">{{ formatCurrency(animal.price) }}</p></div>
        <div><p class="text-slate-500">Supplier</p><p class="font-medium">{{ animal.supplier }}</p></div>
        <div><p class="text-slate-500">Lokasi</p><p class="font-medium">{{ animal.location }}</p></div>
        <div><p class="text-slate-500">Tipe Sembelih</p><p class="font-medium">{{ animal.slaughter_type }}</p></div>
        <div><p class="text-slate-500">Vendor</p><p class="font-medium">{{ animal.vendor ?? "—" }}</p></div>
        <div><p class="text-slate-500">Biaya Vendor</p><p class="font-medium">{{ animal.vendor_cost ? formatCurrency(animal.vendor_cost) : "—" }}</p></div>
        <div><p class="text-slate-500">Jadwal Jemput</p><p class="font-medium">{{ animal.pickup_schedule ?? "—" }}</p></div>
      </div>

      <!-- Participants list -->
      <div v-if="animal.participants?.length" class="mt-6 border-t pt-4">
        <p class="mb-2 text-sm font-semibold text-slate-700">Peserta ({{ animal.participants.length }})</p>
        <table class="w-full text-left text-sm">
          <thead class="border-b text-xs uppercase text-slate-500">
            <tr><th class="py-2 pr-4">Nama</th><th class="py-2 pr-4">Slot</th><th class="py-2 pr-4">Dibayar</th><th class="py-2">Status</th></tr>
          </thead>
          <tbody>
            <tr v-for="p in animal.participants" :key="p.id" class="border-b border-slate-50">
              <td class="py-2 pr-4">{{ p.name }}</td>
              <td class="py-2 pr-4">{{ p.slot_number ?? "—" }}</td>
              <td class="py-2 pr-4">{{ formatCurrency(p.amount_paid) }}</td>
              <td class="py-2"><PaymentBadge :status="p.payment_status" /></td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Slaughtering info -->
      <div v-if="animal.slaughtering" class="mt-6 border-t pt-4">
        <p class="mb-2 text-sm font-semibold text-slate-700">Penyembelihan</p>
        <div class="grid gap-2 text-sm md:grid-cols-3">
          <p>Tanggal: <span class="font-medium">{{ animal.slaughtering.date }}</span></p>
          <p>Lokasi: <span class="font-medium">{{ animal.slaughtering.location }}</span></p>
          <p>Daging: <span class="font-medium">{{ animal.slaughtering.meat_total_kg ?? "—" }} kg</span></p>
          <p>Distribusi: <Badge size="sm">{{ animal.slaughtering.distribution_status }}</Badge></p>
        </div>
      </div>
    </Card>

    <!-- Edit Form -->
    <Card v-else class="p-6">
      <p class="mb-4 text-sm font-semibold text-slate-800">Edit Hewan #{{ animal.id }}</p>
      <div class="grid gap-4 md:grid-cols-3">
        <div>
          <label class="mb-1 block text-xs font-medium text-slate-600">Tipe</label>
          <Select v-model="form.type"><option value="sapi">sapi</option><option value="kambing">kambing</option></Select>
        </div>
        <div>
          <label class="mb-1 block text-xs font-medium text-slate-600">Berat (kg)</label>
          <Input v-model="form.weight" />
        </div>
        <div>
          <label class="mb-1 block text-xs font-medium text-slate-600">Harga</label>
          <Input v-model="form.price" />
        </div>
        <div>
          <label class="mb-1 block text-xs font-medium text-slate-600">Supplier</label>
          <Input v-model="form.supplier" />
        </div>
        <div>
          <label class="mb-1 block text-xs font-medium text-slate-600">Lokasi</label>
          <Input v-model="form.location" />
        </div>
        <div>
          <label class="mb-1 block text-xs font-medium text-slate-600">Tipe Sembelih</label>
          <Select v-model="form.slaughter_type"><option value="onsite">onsite</option><option value="offsite">offsite</option></Select>
        </div>
        <div>
          <label class="mb-1 block text-xs font-medium text-slate-600">Vendor</label>
          <Input v-model="form.vendor" />
        </div>
        <div>
          <label class="mb-1 block text-xs font-medium text-slate-600">Biaya Vendor</label>
          <Input v-model="form.vendor_cost" />
        </div>
        <div>
          <label class="mb-1 block text-xs font-medium text-slate-600">Status</label>
          <Select v-model="form.status">
            <option value="available">tersedia</option>
            <option value="assigned">terisi</option>
            <option value="slaughtered">disembelih</option>
            <option value="distributed">didistribusi</option>
          </Select>
        </div>
      </div>
      <div class="mt-4 flex gap-3">
        <Button @click="submitEdit" :disabled="form.processing">Simpan</Button>
        <Button variant="outline" @click="editing = false">Batal</Button>
      </div>
    </Card>

  </AuthenticatedLayout>
</template>
