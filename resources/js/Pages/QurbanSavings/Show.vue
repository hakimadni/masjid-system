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

const props = defineProps({ saving: Object })

const editing = ref(false)
const form = useForm({
  target_amount: props.saving.target_amount ?? "",
  status: props.saving.status ?? "active",
})
const submitEdit = () => form.put(route("savings.update", props.saving.id), { onSuccess: () => { editing.value = false } })
const destroy = () => { if (confirm("Hapus tabungan ini?")) useForm({}).delete(route("savings.destroy", props.saving.id)) }

const formatCurrency = (v) => new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", maximumFractionDigits: 0 }).format(Number(v || 0))
</script>

<template>
  <Head :title="'Tabungan #' + saving.id" />
  <AuthenticatedLayout :title="'Tabungan #' + saving.id">

    <div class="mb-4 flex items-center gap-3">
      <Link :href="route('savings.index')"><Button variant="ghost">&larr; Kembali</Button></Link>
      <Button v-if="!editing" size="sm" @click="editing = true">Edit</Button>
      <Button v-else size="sm" variant="outline" @click="editing = false">Batal</Button>
      <Button size="sm" variant="danger" @click="destroy">Hapus</Button>
    </div>

    <Card v-if="!editing" class="p-6">
      <div class="mb-4 flex items-center gap-2">
        <Badge :variant="saving.status === 'active' ? 'info' : saving.status === 'completed' ? 'success' : 'danger'">{{ saving.status }}</Badge>
      </div>
      <div class="grid gap-4 text-sm md:grid-cols-3">
        <div><p class="text-slate-500">Pemilik</p><p class="font-medium">{{ saving.user?.name ?? "User #" + saving.user_id }}</p></div>
        <div><p class="text-slate-500">Target</p><p class="font-medium">{{ formatCurrency(saving.target_amount) }}</p></div>
        <div><p class="text-slate-500">Saldo Saat Ini</p><p class="font-medium">{{ formatCurrency(saving.current_balance) }}</p></div>
        <div><p class="text-slate-500">Sisa</p><p class="font-medium">{{ formatCurrency(saving.remaining_amount) }}</p></div>
        <div><p class="text-slate-500">Progress</p><p class="font-medium">{{ saving.progress_percentage }}%</p></div>
        <div><p class="text-slate-500">Eligible Kambing</p><p class="font-medium">{{ saving.eligible_kambing ?? 0 }}</p></div>
        <div><p class="text-slate-500">Eligible Sapi Share</p><p class="font-medium">{{ saving.eligible_sapi_share ?? 0 }}</p></div>
      </div>

      <div v-if="saving.participants?.length" class="mt-6 border-t pt-4">
        <p class="mb-2 text-sm font-semibold text-slate-700">Peserta Terkait ({{ saving.participants.length }})</p>
        <table class="w-full text-left text-sm">
          <thead class="border-b text-xs uppercase text-slate-500">
            <tr><th class="py-2 pr-4">Nama</th><th class="py-2 pr-4">Hewan</th><th class="py-2 pr-4">Bayar</th><th class="py-2">Status</th></tr>
          </thead>
          <tbody>
            <tr v-for="p in saving.participants" :key="p.id" class="border-b border-slate-50">
              <td class="py-2 pr-4">{{ p.name }}</td>
              <td class="py-2 pr-4">#{{ p.animal_id }}</td>
              <td class="py-2 pr-4">{{ formatCurrency(p.amount_paid) }} / {{ formatCurrency(p.amount_due) }}</td>
              <td class="py-2"><PaymentBadge :status="p.payment_status" /></td>
            </tr>
          </tbody>
        </table>
      </div>
      <p v-else class="mt-4 text-xs text-slate-400">Belum ada peserta terhubung ke tabungan ini.</p>
    </Card>

    <Card v-else class="p-6">
      <p class="mb-4 text-sm font-semibold text-slate-800">Edit Tabungan #{{ saving.id }}</p>
      <div class="grid gap-4 md:grid-cols-2">
        <div>
          <label class="mb-1 block text-xs font-medium text-slate-600">Target</label>
          <Input v-model="form.target_amount" />
        </div>
        <div>
          <label class="mb-1 block text-xs font-medium text-slate-600">Status</label>
          <Select v-model="form.status">
            <option value="active">Active</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
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
