<script setup>
import Dialog from "@/Components/ui/dialog/Dialog.vue"
import MobileFab from "@/Components/MobileFab.vue"
import PaymentBadge from "@/Components/qurban/PaymentBadge.vue"
import { computed, reactive, ref } from "vue"
import { Head, Link, useForm, usePage } from "@inertiajs/vue3"
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import DataTable from "@/Components/ui/table/DataTable.vue"
import ToastMessage from "@/Components/ui/toast/ToastMessage.vue"
import Card from "@/Components/ui/card/Card.vue"
import Button from "@/Components/ui/button/Button.vue"
import Input from "@/Components/ui/input/Input.vue"
import Select from "@/Components/ui/select/Select.vue"
import Badge from "@/Components/ui/badge/Badge.vue"
import StatCard from "@/Components/qurban/StatCard.vue"

const props = defineProps({
  savings: Object,
  users: Array,
  summary: Object,
})

const page = usePage()
const flash = computed(() => page.props.flash ?? {})

const createDialogOpen = ref(false)
const createForm = useForm({
  user_id: "",
  target_amount: "",
  status: "active",
})

const submitCreate = () => createForm.post(route("savings.store"), { preserveScroll: true })

const txForms = reactive({})
const editTarget = reactive({})
const selectedId = ref(null)

const quickTargets = [
  { label: "Kambing", value: 2500000 },
  { label: "Sapi 1/7", value: 3000000 },
  { label: "Custom", value: null },
]

const selectedSaving = computed(() => props.savings.data.find((item) => item.id === selectedId.value) ?? null)

const ensureForms = (item) => {
  if (!txForms[item.id]) {
    txForms[item.id] = { topup: "", withdraw: "" }
  }
  if (!editTarget[item.id]) {
    editTarget[item.id] = item.target_amount
  }
}

props.savings.data.forEach((item) => ensureForms(item))

const formatCurrency = (value) =>
  new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    maximumFractionDigits: 0,
  }).format(Number(value || 0))

const statusVariant = (status) => {
  if (status === "completed") return "success"
  if (status === "cancelled") return "danger"
  return "info"
}

const chooseQuickTarget = (value) => {
  if (value !== null) {
    createForm.target_amount = String(value)
  }
}

const submitTopup = (savingId) => {
  const amount = Number(txForms[savingId]?.topup || 0)
  if (amount <= 0) return

  useForm({ amount }).post(route("savings.topup", savingId), {
    preserveScroll: true,
    onSuccess: () => {
      txForms[savingId].topup = ""
      selectedId.value = savingId
    },
  })
}

const submitWithdraw = (savingId) => {
  const amount = Number(txForms[savingId]?.withdraw || 0)
  if (amount <= 0) return

  useForm({ amount }).post(route("savings.withdraw", savingId), {
    preserveScroll: true,
    onSuccess: () => {
      txForms[savingId].withdraw = ""
      selectedId.value = savingId
    },
  })
}

const updateTarget = (savingId) => {
  const target = Number(editTarget[savingId] || 0)
  if (target <= 0) return

  useForm({
    target_amount: target,
    status: "active",
  }).put(route("savings.update", savingId), {
    preserveScroll: true,
  })
}

const updateStatus = (savingId, status) => {
  const saving = props.savings.data.find((item) => item.id === savingId)
  if (!saving) return

  useForm({
    target_amount: Number(saving.target_amount),
    status,
  }).put(route("savings.update", savingId), {
    preserveScroll: true,
  })
}

const deleteSaving = (savingId) => {
  if (!confirm("Hapus tabungan ini?")) return
  useForm({}).delete(route("savings.destroy", savingId), { preserveScroll: true })
}
</script>

<template>
  <Head title="Qurban" />
  <AuthenticatedLayout title="Qurban">

    <div class="space-y-6">
      <div v-if="flash.success" class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
        {{ flash.success }}
      </div>

      <div class="grid gap-4 md:grid-cols-5">
        <StatCard title="Total Rekening" :value="summary.total_accounts" />
        <StatCard title="Total Saldo" :value="formatCurrency(summary.total_balance)" />
        <StatCard title="Aktif" :value="summary.active_accounts" />
        <StatCard title="Selesai" :value="summary.completed_accounts" />
        <StatCard title="Rata-rata Progress" :value="`${Number(summary.avg_progress || 0).toFixed(1)}%`" />
      </div>

      

      <DataTable>
        <thead class="bg-slate-50 text-xs uppercase text-slate-500">
          <tr>
            <th class="px-4 py-3 text-left">User</th>
            <th class="px-4 py-3 text-left">Target</th>
            <th class="px-4 py-3 text-left">Saldo</th>
            <th class="px-4 py-3 text-left">Progress</th>
            <th class="px-4 py-3 text-left">Status</th>
            <th class="px-4 py-3 text-left">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="item in savings.data"
            :key="item.id"
            class="border-t border-slate-100 align-top hover:bg-slate-50/80"
            @mouseenter="ensureForms(item)"
          >
            <td class="px-4 py-3">
              <p class="font-medium text-slate-800">{{ item.user?.name ?? `User #${item.user_id}` }}</p>
            </td>
            <td class="px-4 py-3">
              <p>{{ formatCurrency(item.target_amount) }}</p>
              <div class="mt-2 flex items-center gap-2">
                <Input v-model="editTarget[item.id]" class="h-8" />
                <Button size="sm" variant="outline" @click="updateTarget(item.id)">Set Target</Button>
              </div>
            </td>
            <td class="px-4 py-3">
              <p class="font-medium">{{ formatCurrency(item.current_balance) }}</p>
              <p class="text-xs text-slate-500">Sisa {{ formatCurrency(item.remaining_amount) }}</p>
            </td>
            <td class="px-4 py-3">
              <div class="h-2 w-44 rounded-full bg-slate-200">
                <div class="h-2 rounded-full bg-slate-900" :style="`width: ${item.progress_percentage}%`" />
              </div>
              <p class="mt-1 text-xs text-slate-600">{{ item.progress_percentage }}%</p>
              <div class="mt-2 flex gap-1">
                <Badge :variant="item.eligible_kambing ? 'success' : 'default'">Kambing</Badge>
                <Badge :variant="item.eligible_sapi_share ? 'success' : 'default'">Sapi 1/7</Badge>
              </div>
            </td>
            <td class="px-4 py-3">
              <Badge :variant="statusVariant(item.status)">{{ item.status }}</Badge>
              <div class="mt-2 flex gap-2">
                <Button size="sm" variant="outline" @click="updateStatus(item.id, 'active')">Aktif</Button>
                <Button size="sm" variant="outline" @click="updateStatus(item.id, 'completed')">Selesai</Button>
                <Button size="sm" variant="outline" @click="updateStatus(item.id, 'cancelled')">Batal</Button>
              </div>
            </td>
            <td class="space-y-2 px-4 py-3">
              <div class="flex items-center gap-2">
                <Input v-model="txForms[item.id].topup" class="h-8" placeholder="Top up" />
                <Button size="sm" @click="submitTopup(item.id)">Top up</Button>
              </div>
              <div class="flex items-center gap-2">
                <Input v-model="txForms[item.id].withdraw" class="h-8" placeholder="Withdraw" />
                <Button size="sm" variant="outline" @click="submitWithdraw(item.id)">Withdraw</Button>
              </div>
              <Link :href="route('savings.show', item.id)"><Button size="sm" variant="secondary">Detail</Button></Link>
              <Button size="sm" variant="destructive" @click="deleteSaving(item.id)">Hapus</Button>
            </td>
          </tr>
        </tbody>
      </DataTable>

      <Card v-if="selectedSaving" class="p-5">
        <p class="text-sm font-semibold text-slate-800">Detail Tabungan #{{ selectedSaving.id }}</p>
        <div class="mt-3 grid gap-3 text-sm md:grid-cols-2">
          <p>Nama: <span class="font-medium">{{ selectedSaving.user?.name }}</span></p>
          <p>Target: <span class="font-medium">{{ formatCurrency(selectedSaving.target_amount) }}</span></p>
          <p>Saldo: <span class="font-medium">{{ formatCurrency(selectedSaving.current_balance) }}</span></p>
          <p>Sisa: <span class="font-medium">{{ formatCurrency(selectedSaving.remaining_amount) }}</span></p>
          <p>Peserta: <span class="font-medium">{{ selectedSaving.participants_count ?? 0 }} orang</span></p>
        </div>

        <div v-if="selectedSaving.participants?.length" class="mt-4">
          <p class="mb-2 text-sm font-semibold text-slate-700">Peserta Terkait</p>
          <table class="w-full text-left text-sm">
            <thead class="border-b text-xs uppercase text-slate-500">
              <tr>
                <th class="py-2 pr-4">Nama</th>
                <th class="py-2 pr-4">Hewan</th>
                <th class="py-2 pr-4">Bayar</th>
                <th class="py-2">Status</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="p in selectedSaving.participants" :key="p.id" class="border-b border-slate-50">
                <td class="py-2 pr-4">{{ p.name }}</td>
                <td class="py-2 pr-4">#{{ p.animal_id }}</td>
                <td class="py-2 pr-4">{{ formatCurrency(p.amount_paid) }} / {{ formatCurrency(p.amount_due) }}</td>
                <td class="py-2"><PaymentBadge :status="p.payment_status" /></td>
              </tr>
            </tbody>
          </table>
        </div>
        <p v-else class="mt-3 text-xs text-slate-400">Belum ada peserta yang terhubung ke tabungan ini.</p>
      </Card>
    </div>
  
    <MobileFab @click="createDialogOpen = true" />
    <Dialog :open="createDialogOpen" @close="createDialogOpen = false">
      <div class="p-5 max-h-[85vh] overflow-y-auto">
        <p class="text-sm font-medium text-slate-700">Buat Tabungan Baru</p>

        <div class="mt-3 flex flex-wrap gap-2">
          <Button v-for="item in quickTargets" :key="item.label" variant="outline" size="sm" @click="chooseQuickTarget(item.value)">
            {{ item.label }}
          </Button>
        </div>

        <div class="mt-4 grid gap-3 md:grid-cols-4">
          <Select v-model="createForm.user_id">
            <option disabled value="">Pilih User</option>
            <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
          </Select>
          <Input v-model="createForm.target_amount" placeholder="Target Amount (IDR)" />
          <Select v-model="createForm.status">
            <option value="active">Aktif</option>
            <option value="cancelled">Dibatalkan</option>
          </Select>
          <Button @click="submitCreate">Simpan</Button>
        </div>
      </div>
    </Dialog>
  </AuthenticatedLayout>

</template>