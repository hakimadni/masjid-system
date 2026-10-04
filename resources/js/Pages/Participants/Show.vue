<script setup>
import { ref } from "vue"
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import PaymentBadge from "@/Components/qurban/PaymentBadge.vue"
import Card from "@/Components/ui/card/Card.vue"
import Button from "@/Components/ui/button/Button.vue"
import Input from "@/Components/ui/input/Input.vue"
import Select from "@/Components/ui/select/Select.vue"
import Badge from "@/Components/ui/badge/Badge.vue"
import { Head, Link, useForm } from "@inertiajs/vue3"

const props = defineProps({ participant: Object, animals: Array, savings: Array })

const editing = ref(false)
const form = useForm({
  animal_id: props.participant.animal_id ?? "",
  qurban_saving_id: props.participant.qurban_saving_id ?? "",
  name: props.participant.name,
  phone: props.participant.phone ?? "",
  amount_due: props.participant.amount_due ?? "",
  amount_paid: props.participant.amount_paid ?? "",
  payment_status: props.participant.payment_status ?? "unpaid",
})
const submitEdit = () => form.put(route("participants.update", props.participant.id), { onSuccess: () => { editing.value = false } })
const destroy = () => { if (confirm("Hapus peserta ini?")) useForm({}).delete(route("participants.destroy", props.participant.id)) }

const formatCurrency = (v) => new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", maximumFractionDigits: 0 }).format(Number(v || 0))
const animalLabel = (a) => `#${a.id} — ${a.type} (${a.weight}kg) — ${a.supplier || "-"}`
const savingLabel = (s) => `#${s.id} — ${s.user?.name ?? "User #" + s.user_id} — ${formatCurrency(s.current_balance)}`
</script>

<template>
  <Head :title="'Peserta: ' + participant.name" />
  <AuthenticatedLayout :title="'Peserta: ' + participant.name">

    <div class="mb-4 flex items-center gap-3">
      <Link :href="route('participants.index')"><Button variant="ghost">&larr; Kembali</Button></Link>
      <Button v-if="!editing" size="sm" @click="editing = true">Edit</Button>
      <Button v-else size="sm" variant="outline" @click="editing = false">Batal</Button>
      <Button size="sm" variant="danger" @click="destroy">Hapus</Button>
    </div>

    <Card v-if="!editing" class="p-6">
      <div class="mb-4 flex items-center gap-3">
        <Badge>{{ participant.payment_status }}</Badge>
        <PaymentBadge :status="participant.payment_status" />
      </div>
      <div class="grid gap-4 text-sm md:grid-cols-3">
        <div><p class="text-slate-500">Nama</p><p class="font-medium">{{ participant.name }}</p></div>
        <div><p class="text-slate-500">Telepon</p><p class="font-medium">{{ participant.phone }}</p></div>
        <div><p class="text-slate-500">Slot</p><p class="font-medium">{{ participant.slot_number ?? "—" }}</p></div>
        <div><p class="text-slate-500">Hewan</p>
          <p class="font-medium">
            <template v-if="participant.animal">
              <Badge>{{ participant.animal.type }}</Badge> #{{ participant.animal_id }}
            </template>
            <span v-else>#{{ participant.animal_id }}</span>
          </p>
        </div>
        <div><p class="text-slate-500">Tabungan</p>
          <p class="font-medium">
            <template v-if="participant.qurban_saving">
              #{{ participant.qurban_saving.id }} — {{ participant.qurban_saving.user?.name ?? "—" }} — {{ formatCurrency(participant.qurban_saving.current_balance) }}
            </template>
            <span v-else class="text-slate-400">Tidak terhubung</span>
          </p>
        </div>
        <div><p class="text-slate-500">Tagihan</p><p class="font-medium">{{ formatCurrency(participant.amount_due) }}</p></div>
        <div><p class="text-slate-500">Terbayar</p><p class="font-medium">{{ formatCurrency(participant.amount_paid) }}</p></div>
      </div>
    </Card>

    <Card v-else class="p-6">
      <p class="mb-4 text-sm font-semibold text-slate-800">Edit Peserta #{{ participant.id }}</p>
      <div class="grid gap-4 md:grid-cols-3">
        <div>
          <label class="mb-1 block text-xs font-medium text-slate-600">Nama</label>
          <Input v-model="form.name" />
        </div>
        <div>
          <label class="mb-1 block text-xs font-medium text-slate-600">Telepon</label>
          <Input v-model="form.phone" />
        </div>
        <div>
          <label class="mb-1 block text-xs font-medium text-slate-600">Hewan</label>
          <Select v-model="form.animal_id">
            <option disabled value="">Pilih Hewan</option>
            <option v-for="a in animals" :key="a.id" :value="a.id">{{ animalLabel(a) }}</option>
          </Select>
        </div>
        <div>
          <label class="mb-1 block text-xs font-medium text-slate-600">Tabungan</label>
          <Select v-model="form.qurban_saving_id">
            <option value="">— Tanpa Tabungan —</option>
            <option v-for="s in savings" :key="s.id" :value="s.id">{{ savingLabel(s) }}</option>
          </Select>
        </div>
        <div>
          <label class="mb-1 block text-xs font-medium text-slate-600">Tagihan</label>
          <Input v-model="form.amount_due" />
        </div>
        <div>
          <label class="mb-1 block text-xs font-medium text-slate-600">Terbayar</label>
          <Input v-model="form.amount_paid" />
        </div>
        <div>
          <label class="mb-1 block text-xs font-medium text-slate-600">Status Bayar</label>
          <Select v-model="form.payment_status">
            <option value="unpaid">Belum Bayar</option>
            <option value="partial">Sebagian</option>
            <option value="paid">Lunas</option>
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
