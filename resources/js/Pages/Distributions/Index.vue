<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import DataTable from "@/Components/ui/table/DataTable.vue"
import Card from "@/Components/ui/card/Card.vue"
import Button from "@/Components/ui/button/Button.vue"
import Input from "@/Components/ui/input/Input.vue"
import Select from "@/Components/ui/select/Select.vue"
import Badge from "@/Components/ui/badge/Badge.vue"
import { Head, Link, useForm } from "@inertiajs/vue3"

const props = defineProps({ distributions: Object, slaughterings: Array })

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
      <Card class="p-5">
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

      <DataTable>
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
  </AuthenticatedLayout>
</template>
