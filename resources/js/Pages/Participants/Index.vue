<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import PaymentBadge from "@/Components/qurban/PaymentBadge.vue"
import DataTable from "@/Components/ui/table/DataTable.vue"
import Card from "@/Components/ui/card/Card.vue"
import Button from "@/Components/ui/button/Button.vue"
import Input from "@/Components/ui/input/Input.vue"
import Select from "@/Components/ui/select/Select.vue"
import Badge from "@/Components/ui/badge/Badge.vue"
import { Head, Link, useForm } from "@inertiajs/vue3"

const props = defineProps({ participants: Object, animals: Array, savings: Array })

const form = useForm({
  animal_id: "",
  qurban_saving_id: "",
  name: "",
  phone: "",
  amount_due: "",
  payment_status: "unpaid",
})
const submit = () => form.post(route("participants.store"))
const runAutoGroup = () => useForm({}).post(route("participants.auto-group"))

const animalLabel = (a) => `#${a.id} — ${a.type} (${a.weight}kg) — ${a.supplier || "-"}`
const savingLabel = (s) => `#${s.id} — ${s.user?.name ?? "User #" + s.user_id} — Rp ${Number(s.current_balance || 0).toLocaleString("id-ID")} (${s.status})`
const paymentStatusVariant = (s) => {
  if (s === "paid") return "success"
  if (s === "partial") return "warning"
  return "default"
}
const formatCurrency = (v) => new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", maximumFractionDigits: 0 }).format(Number(v || 0))
</script>

<template>
  <Head title="Peserta Qurban" />
  <AuthenticatedLayout title="Peserta Qurban">

    <div class="space-y-6">
      <Card class="p-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
          <p class="text-sm font-medium text-slate-700">Tambah Peserta</p>
          <Button variant="outline" @click="runAutoGroup">Auto Group Sapi (7 peserta)</Button>
        </div>

        <div class="mt-4 grid gap-3 md:grid-cols-6">
          <Select v-model="form.animal_id">
            <option disabled value="">Pilih Hewan</option>
            <option v-for="a in animals" :key="a.id" :value="a.id">{{ animalLabel(a) }}</option>
          </Select>
          <Select v-model="form.qurban_saving_id">
            <option value="">— Tanpa Tabungan —</option>
            <option v-for="s in savings" :key="s.id" :value="s.id">{{ savingLabel(s) }}</option>
          </Select>
          <Input v-model="form.name" placeholder="Nama" />
          <Input v-model="form.phone" placeholder="Nomor Telepon" />
          <Input v-model="form.amount_due" placeholder="Tagihan" />
          <Button @click="submit">Simpan</Button>
        </div>
      </Card>

      <DataTable>
        <thead class="bg-slate-50 text-xs uppercase text-slate-500">
          <tr>
            <th class="px-4 py-3 text-left">Nama</th>
            <th class="px-4 py-3 text-left">Hewan</th>
            <th class="px-4 py-3 text-left">Tabungan</th>
            <th class="px-4 py-3 text-left">Tagihan</th>
            <th class="px-4 py-3 text-left">Status</th>
            <th class="px-4 py-3 text-left"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in participants.data" :key="item.id" class="border-t border-slate-100 hover:bg-slate-50/80">
            <td class="px-4 py-3">
              <p class="font-medium text-slate-800">{{ item.name }}</p>
              <p class="text-xs text-slate-500">{{ item.phone }}</p>
            </td>
            <td class="px-4 py-3">
              <template v-if="item.animal">
                <Badge>{{ item.animal.type }}</Badge>
                <p class="mt-1 text-xs text-slate-600">#{{ item.animal_id }}</p>
              </template>
              <span v-else class="text-xs text-slate-400">#{{ item.animal_id }}</span>
              <p v-if="item.slot_number" class="text-xs text-slate-500">Slot {{ item.slot_number }}/7</p>
            </td>
            <td class="px-4 py-3">
              <template v-if="item.qurban_saving">
                <p class="text-xs font-medium text-slate-700">{{ item.qurban_saving.user?.name ?? "—" }}</p>
                <p class="text-xs text-slate-500">{{ formatCurrency(item.qurban_saving.current_balance) }}</p>
                <Badge size="sm" :variant="item.qurban_saving.status === 'active' ? 'info' : item.qurban_saving.status === 'completed' ? 'success' : 'danger'">{{ item.qurban_saving.status }}</Badge>
              </template>
              <span v-else class="text-xs text-slate-400">—</span>
            </td>
            <td class="px-4 py-3">
              <p class="text-sm">{{ formatCurrency(item.amount_due) }}</p>
              <p class="text-xs text-slate-500">Dibayar: {{ formatCurrency(item.amount_paid) }}</p>
            </td>
            <td class="px-4 py-3">
              <PaymentBadge :status="item.payment_status" />
            </td>
            <td class="px-4 py-3">
              <Link :href="route('participants.show', item.id)">
                <Button size="sm" variant="outline">Detail</Button>
              </Link>
            </td>
          </tr>
        </tbody>
      </DataTable>
    </div>
  </AuthenticatedLayout>
</template>
