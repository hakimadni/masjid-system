<script setup>
import { ref } from "vue"
import { Head, useForm } from "@inertiajs/vue3"
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import StatCard from "@/Components/qurban/StatCard.vue"
import Card from "@/Components/ui/card/Card.vue"
import CardHeader from "@/Components/ui/card/CardHeader.vue"
import CardTitle from "@/Components/ui/card/CardTitle.vue"
import CardContent from "@/Components/ui/card/CardContent.vue"
import Button from "@/Components/ui/button/Button.vue"
import Dialog from "@/Components/ui/dialog/Dialog.vue"
import Input from "@/Components/ui/input/Input.vue"

const props = defineProps({
  wakif: { type: Array, default: () => [] },
  records: { type: Array, default: () => [] },
  usages: { type: Array, default: () => [] },
  summary: { type: Object, default: () => ({}) },
})

const wakifForm = useForm({
  name: "",
  phone: "",
  email: "",
  address: "",
  display_publicly: false,
})

const recordForm = useForm({
  wakaf_wakif_id: "",
  wakaf_type: "uang",
  amount: "",
  asset_description: "",
  purpose: "",
  pledged_date: new Date().toISOString().slice(0, 10),
})

const usageForm = useForm({
  wakaf_record_id: "",
  amount: "",
  description: "",
  usage_date: new Date().toISOString().slice(0, 10),
})

const showWakifDialog = ref(false)
const showRecordDialog = ref(false)
const showUsageDialog = ref(false)

const formatCurrency = (value) =>
  new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    maximumFractionDigits: 0,
  }).format(Number(value || 0))

const submitWakif = () => {
  wakifForm.post(route("wakaf.wakif.store"), {
    onSuccess: () => {
      showWakifDialog.value = false
      wakifForm.reset()
    }
  })
}

const submitRecord = () => {
  recordForm.post(route("wakaf.store"), {
    onSuccess: () => {
      showRecordDialog.value = false
      recordForm.reset()
    }
  })
}

const submitUsage = () => {
  usageForm.post(route("wakaf.usage.store"), {
    onSuccess: () => {
      showUsageDialog.value = false
      usageForm.reset()
    }
  })
}
</script>

<template>
  <Head title="Wakaf" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-slate-900">Wakaf</h2>
    </template>

    <div class="py-6 space-y-6">
      <!-- Summary Stats -->
      <div class="grid md:grid-cols-4 gap-4">
        <StatCard title="Total Wakaf Masuk" :value="formatCurrency(summary.total_received)" />
        <StatCard title="Total Dialokasikan" :value="formatCurrency(summary.total_allocated)" />
        <StatCard title="Total Selesai" :value="formatCurrency(summary.total_completed)" />
        <StatCard title="Total Digunakan" :value="formatCurrency(summary.total_usage)" />
      </div>

      <!-- Wakaf Records -->
      

      <!-- Wakaf Dialog -->
      <Dialog v-model:open="showRecordDialog">
        <form @submit.prevent="submitRecord" class="p-6">
          <h3 class="text-lg font-semibold mb-4">Tambah Wakaf</h3>
          <div class="space-y-3">
            <select v-model="recordForm.wakaf_type" class="w-full border rounded px-3 py-2">
              <option value="uang">Uang</option>
              <option value="aset">Aset</option>
              <option value="fidiyah">Fidiyah</option>
            </select>
            <Input v-model="recordForm.amount" type="number" step="1000" placeholder="Jumlah (Rp)" />
            <Input v-model="recordForm.asset_description" placeholder="Deskripsi aset (jika aset)" />
            <Input v-model="recordForm.purpose" placeholder="Tujuan wakaf" />
            <Input v-model="recordForm.pledged_date" type="date" />
            <Button type="submit" :disabled="recordForm.processing">Simpan</Button>
          </div>
        </form>
      </Dialog>
    </div>
  
    <MobileFab @click="createDialogOpen = true" />
    <Dialog :open="createDialogOpen" @close="createDialogOpen = false">
      <div class="p-5 max-h-[85vh] overflow-y-auto">
        <CardHeader class="flex flex-row items-center justify-between">
          <CardTitle>Daftar Wakaf</CardTitle>
          <Button @click="showRecordDialog = true" size="sm">Tambah Wakaf</Button>
        </CardHeader>
        <CardContent>
          <div class="overflow-x-auto">
            <table class="w-full text-sm">
              <thead>
                <tr class="border-b">
                  <th class="text-left py-2">Wakif</th>
                  <th class="text-left py-2">Tipe</th>
                  <th class="text-right py-2">Jumlah (Rp)</th>
                  <th class="text-center py-2">Status</th>
                  <th class="text-center py-2">Tgl Janji</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in records" :key="item.id" class="border-b">
                  <td class="py-2">{{ item.wakif?.name ?? 'Umum' }}</td>
                  <td class="py-2 capitalize">{{ item.wakaf_type }}</td>
                  <td class="py-2 text-right">{{ formatCurrency(item.amount) }}</td>
                  <td class="py-2 text-center capitalize">{{ item.status }}</td>
                  <td class="py-2 text-center">{{ item.pledged_date }}</td>
                </tr>
                <tr v-if="!records.length">
                  <td colspan="5" class="py-8 text-center text-slate-500">Belum ada data wakaf</td>
                </tr>
              </tbody>
            </table>
          </div>
        </CardContent>
      </div>
    </Dialog>
  </AuthenticatedLayout>

</template>