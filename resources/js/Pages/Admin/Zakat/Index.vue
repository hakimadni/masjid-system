<script setup>
import { computed, ref } from "vue"
import { Head, useForm, usePage, Link } from "@inertiajs/vue3"
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
  muzakki: { type: Array, default: () => [] },
  mustahik: { type: Array, default: () => [] },
  distributions: { type: Array, default: () => [] },
  summary: { type: Object, default: () => ({}) },
})

const muzakkiForm = useForm({
  name: "",
  phone: "",
  zakat_type: "fitrah",
  payment_form: "uang",
  money_amount: "",
  rice_amount: "",
  payment_date: new Date().toISOString().slice(0, 10),
  notes: "",
})

const mustahikForm = useForm({
  name: "",
  phone: "",
  address: "",
  category: "",
  notes: "",
})

const distributionForm = useForm({
  mustahik_id: "",
  zakat_type: "fitrah",
  money_amount: "",
  rice_amount: "",
  distribution_date: new Date().toISOString().slice(0, 10),
  received: false,
  notes: "",
})

const showMuzakkiDialog = ref(false)
const showMustahikDialog = ref(false)
const showDistributionDialog = ref(false)

const formatCurrency = (value) =>
  new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    maximumFractionDigits: 0,
  }).format(Number(value || 0))

const submitMuzakki = () => {
  muzakkiForm.post(route("zakat.muzakki.store"), {
    onSuccess: () => {
      showMuzakkiDialog.value = false
      muzakkiForm.reset()
    }
  })
}

const submitMustahik = () => {
  mustahikForm.post(route("zakat.mustahik.store"), {
    onSuccess: () => {
      showMustahikDialog.value = false
      mustahikForm.reset()
    }
  })
}

const submitDistribution = () => {
  distributionForm.post(route("zakat.distribution.store"), {
    onSuccess: () => {
      showDistributionDialog.value = false
      distributionForm.reset()
    }
  })
}
</script>

<template>
  <Head title="Zakat" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-slate-900">Zakat</h2>
    </template>

    <div class="py-6 space-y-6">
      <!-- Summary Stats -->
      <div class="grid md:grid-cols-4 gap-4">
        <StatCard title="Total Zakat Fitrah" :value="formatCurrency(summary.total_fitrah)" />
        <StatCard title="Total Zakat Mal" :value="formatCurrency(summary.total_mal)" />
        <StatCard title="Total Beras" :value="`${summary.total_rice || 0} kg`" />
        <StatCard title="Dialihkan (Uang)" :value="formatCurrency(summary.distributed_money)" />
      </div>

      <!-- Muzakki List -->
      <Card>
        <CardHeader class="flex flex-row items-center justify-between">
          <CardTitle>Muzakki (Pembayar Zakat)</CardTitle>
          <Button @click="showMuzakkiDialog = true" size="sm">Tambah Muzakki</Button>
        </CardHeader>
        <CardContent>
          <div class="overflow-x-auto">
            <table class="w-full text-sm">
              <thead>
                <tr class="border-b">
                  <th class="text-left py-2">Nama</th>
                  <th class="text-left py-2">Tipe</th>
                  <th class="text-right py-2">Jumlah (Rp)</th>
                  <th class="text-right py-2">Beras (kg)</th>
                  <th class="text-center py-2">Status</th>
                  <th class="text-center py-2">Tanggal</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in muzakki" :key="item.id" class="border-b">
                  <td class="py-2">{{ item.name }}</td>
                  <td class="py-2 capitalize">{{ item.zakat_type }}</td>
                  <td class="py-2 text-right">{{ formatCurrency(item.money_amount) }}</td>
                  <td class="py-2 text-right">{{ item.rice_amount }}</td>
                  <td class="py-2 text-center capitalize">{{ item.status }}</td>
                  <td class="py-2 text-center">{{ item.payment_date }}</td>
                </tr>
                <tr v-if="!muzakki.length">
                  <td colspan="6" class="py-8 text-center text-slate-500">Belum ada data muzakki</td>
                </tr>
              </tbody>
            </table>
          </div>
        </CardContent>
      </Card>

      <!-- Mustahik & Distribution -->
      <div class="grid md:grid-cols-2 gap-6">
        <Card>
          <CardHeader class="flex flex-row items-center justify-between">
            <CardTitle>Mustahik (Penerima Zakat)</CardTitle>
            <Button @click="showMustahikDialog = true" size="sm">Tambah</Button>
          </CardHeader>
          <CardContent>
            <ul class="space-y-2">
              <li v-for="item in mustahik" :key="item.id" class="text-sm">
                <strong>{{ item.name }}</strong> - {{ item.category }}
              </li>
              <li v-if="!mustahik.length" class="text-slate-500 text-center py-4">Belum ada mustahik</li>
            </ul>
          </CardContent>
        </Card>

        <Card>
          <CardHeader class="flex flex-row items-center justify-between">
            <CardTitle>Distribusi</CardTitle>
            <Button @click="showDistributionDialog = true" size="sm">Tambah</Button>
          </CardHeader>
          <CardContent>
            <ul class="space-y-2">
              <li v-for="item in distributions" :key="item.id" class="text-sm">
                <strong>{{ item.mustahik?.name ?? '-' }}</strong> - {{ formatCurrency(item.money_amount) }}
              </li>
              <li v-if="!distributions.length" class="text-slate-500 text-center py-4">Belum ada distribusi</li>
            </ul>
          </CardContent>
        </Card>
      </div>

      <!-- Muzakki Dialog -->
      <Dialog v-model:open="showMuzakkiDialog">
        <form @submit.prevent="submitMuzakki" class="p-6">
          <h3 class="text-lg font-semibold mb-4">Tambah Muzakki</h3>
          <div class="space-y-3">
            <Input v-model="muzakkiForm.name" placeholder="Nama muzakki" required />
            <Input v-model="muzakkiForm.phone" placeholder="Telepon" />
            <select v-model="muzakkiForm.zakat_type" class="w-full border rounded px-3 py-2">
              <option value="fitrah">Fitrah</option>
              <option value="mal">Mal</option>
            </select>
            <Input v-model="muzakkiForm.money_amount" type="number" step="1000" placeholder="Jumlah (Rp)" />
            <Input v-model="muzakkiForm.rice_amount" type="number" step="0.1" placeholder="Beras (kg)" />
            <Input v-model="muzakkiForm.payment_date" type="date" />
            <Input v-model="muzakkiForm.notes" placeholder="Catatan" />
            <Button type="submit" :disabled="muzakkiForm.processing">Simpan</Button>
          </div>
        </form>
      </Dialog>
    </div>
  </AuthenticatedLayout>
</template>