<script setup>
import { ref } from "vue"
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import AnimalStatusBadge from "@/Components/qurban/AnimalStatusBadge.vue"
import Card from "@/Components/ui/card/Card.vue"
import Button from "@/Components/ui/button/Button.vue"
import Input from "@/Components/ui/input/Input.vue"
import Select from "@/Components/ui/select/Select.vue"
import Badge from "@/Components/ui/badge/Badge.vue"
import { Head, Link, useForm } from "@inertiajs/vue3"

const props = defineProps({ slaughtering: Object, animals: Array, volunteers: Array })

const editing = ref(false)
const form = useForm({
  animal_id: props.slaughtering.animal_id ?? "",
  date: props.slaughtering.date ?? "",
  location: props.slaughtering.location ?? "",
})
const submitEdit = () => form.put(route("slaughterings.update", props.slaughtering.id), { onSuccess: () => { editing.value = false } })
const destroy = () => { if (confirm("Hapus penyembelihan ini?")) useForm({}).delete(route("slaughterings.destroy", props.slaughtering.id)) }

const formatCurrency = (v) => new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", maximumFractionDigits: 0 }).format(Number(v || 0))
const distStatusVariant = (s) => s === "completed" ? "success" : s === "in_progress" ? "warning" : "default"
const animalLabel = (a) => `#${a.id} — ${a.type} (${a.weight}kg) — ${a.supplier || "-"} [${a.status}]`
</script>

<template>
  <Head :title="'Penyembelihan #' + slaughtering.id" />
  <AuthenticatedLayout :title="'Penyembelihan #' + slaughtering.id">

    <div class="mb-4 flex items-center gap-3">
      <Link :href="route('slaughterings.index')"><Button variant="ghost">&larr; Kembali</Button></Link>
      <Button v-if="!editing" size="sm" @click="editing = true">Edit</Button>
      <Button v-else size="sm" variant="outline" @click="editing = false">Batal</Button>
      <Button size="sm" variant="danger" @click="destroy">Hapus</Button>
    </div>

    <Card v-if="!editing" class="p-6">
      <div class="mb-4 flex items-center gap-2">
        <Badge size="sm" :variant="distStatusVariant(slaughtering.distribution_status)">{{ slaughtering.distribution_status }}</Badge>
      </div>
      <div class="grid gap-4 text-sm md:grid-cols-3">
        <div>
          <p class="text-slate-500">Hewan</p>
          <template v-if="slaughtering.animal">
            <p class="font-medium">
              <Badge>{{ slaughtering.animal.type }}</Badge>
              #{{ slaughtering.animal_id }} · {{ slaughtering.animal.weight }}kg
            </p>
            <p class="text-xs text-slate-500">{{ slaughtering.animal.supplier }} · {{ formatCurrency(slaughtering.animal.price) }}</p>
            <AnimalStatusBadge :status="slaughtering.animal.status" />
          </template>
        </div>
        <div><p class="text-slate-500">Tanggal</p><p class="font-medium">{{ slaughtering.date }}</p></div>
        <div><p class="text-slate-500">Lokasi</p><p class="font-medium">{{ slaughtering.location }}</p></div>
        <div><p class="text-slate-500">Waktu Potong</p><p class="font-medium">{{ slaughtering.cut_time ?? "—" }}</p></div>
        <div><p class="text-slate-500">Total Daging</p><p class="font-medium">{{ slaughtering.meat_total_kg ?? "—" }} kg</p></div>
        <div><p class="text-slate-500">Relawan</p><p class="font-medium">{{ slaughtering.volunteers_count ?? 0 }} orang</p></div>
        <div><p class="text-slate-500">Peserta Hewan</p><p class="font-medium">{{ slaughtering.animal?.participants_count ?? 0 }} orang</p></div>
      </div>

      <div v-if="slaughtering.volunteers?.length" class="mt-6 border-t pt-4">
        <p class="mb-2 text-sm font-semibold text-slate-700">Relawan ({{ slaughtering.volunteers.length }})</p>
        <div class="flex flex-wrap gap-2">
          <Badge v-for="v in slaughtering.volunteers" :key="v.id">{{ v.name }} ({{ v.pivot?.team_role ?? "—" }})</Badge>
        </div>
      </div>
    </Card>

    <Card v-else class="p-6">
      <p class="mb-4 text-sm font-semibold text-slate-800">Edit Penyembelihan #{{ slaughtering.id }}</p>
      <div class="grid gap-4 md:grid-cols-3">
        <div>
          <label class="mb-1 block text-xs font-medium text-slate-600">Hewan</label>
          <Select v-model="form.animal_id">
            <option disabled value="">Pilih Hewan</option>
            <option v-for="a in animals" :key="a.id" :value="a.id">{{ animalLabel(a) }}</option>
          </Select>
        </div>
        <div>
          <label class="mb-1 block text-xs font-medium text-slate-600">Tanggal</label>
          <Input v-model="form.date" type="date" />
        </div>
        <div>
          <label class="mb-1 block text-xs font-medium text-slate-600">Lokasi</label>
          <Input v-model="form.location" />
        </div>
      </div>
      <div class="mt-4 flex gap-3">
        <Button @click="submitEdit" :disabled="form.processing">Simpan</Button>
        <Button variant="outline" @click="editing = false">Batal</Button>
      </div>
    </Card>

  </AuthenticatedLayout>
</template>
