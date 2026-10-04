<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3"
import { ref } from "vue"
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import Card from "@/Components/ui/card/Card.vue"
import Button from "@/Components/ui/button/Button.vue"
import MoneyDisplay from "@/Components/ui/display/MoneyDisplay.vue"
import StatusBadge from "@/Components/ui/status/StatusBadge.vue"
import PageHeader from "@/Components/ui/page/PageHeader.vue"
import ConfirmDialog from "@/Components/ui/dialog/ConfirmDialog.vue"

const props = defineProps({
  donation: { type: Object, required: true },
  canUpdateStatus: { type: Boolean, required: true },
})

const detailRows = [
  ["Nama Donatur", props.donation.donor_name],
  ["Nomor Telepon", props.donation.phone ?? "-"],
  ["Kategori/Kampanye", props.donation.campaign],
  ["Kategori Tabel", props.donation.category],
  ["Status", props.donation.status],
  ["Tanggal", props.donation.donation_date],
  ["Metode Pembayaran", props.donation.method],
  ["Dibuat Oleh", props.donation.creator ?? "-"],
  ["Dikonfirmasi Oleh", props.donation.confirmer ?? "-"],
  ["Waktu Konfirmasi", props.donation.confirmed_at ?? "-"],
]

const confirmAction = ref(false)
const rejectAction = ref(false)

const actionForm = useForm({
  status: "",
})

const submitConfirm = () => {
  actionForm.status = "confirmed"
  actionForm.patch(route("donations.update-status", props.donation.id), {
    preserveScroll: true,
    onSuccess: () => (confirmAction.value = false),
  })
}

const submitReject = () => {
  actionForm.status = "rejected"
  actionForm.patch(route("donations.update-status", props.donation.id), {
    preserveScroll: true,
    onSuccess: () => (rejectAction.value = false),
  })
}
</script>

<template>
  <Head :title="`Detail Donasi`" />

  <AuthenticatedLayout title="Detail Donasi">
    <div class="space-y-6">
      <PageHeader :title="`Donasi ${donation.campaign}`" :description="`Dari ${donation.donor_name}`">
        <template #actions>
          <Link :href="route('donations.index')"><Button variant="outline">Kembali</Button></Link>
          <template v-if="canUpdateStatus">
            <Button
              v-if="donation.can_confirm"
              @click="confirmAction = true"
              class="bg-teal-600 hover:bg-teal-700 text-white"
            >
              Konfirmasi
            </Button>
            <Button
              v-if="donation.can_reject"
              variant="destructive"
              @click="rejectAction = true"
            >
              Tolak
            </Button>
          </template>
        </template>
      </PageHeader>

      <Card class="p-5">
        <div class="grid gap-4 md:grid-cols-2">
          <div>
            <p class="text-sm text-slate-500">Nominal</p>
            <p class="mt-1 text-2xl font-semibold text-slate-900"><MoneyDisplay :value="donation.amount" /></p>
          </div>
          <div>
            <p class="text-sm text-slate-500">Status</p>
            <div class="mt-1"><StatusBadge :status="donation.status" /></div>
          </div>
        </div>
      </Card>

      <Card class="p-5">
        <div class="grid gap-4 md:grid-cols-2">
          <div v-for="row in detailRows" :key="row[0]" class="rounded-xl border border-slate-100 bg-slate-50/70 p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ row[0] }}</p>
            <p v-if="row[0] === 'Status'" class="mt-2"><StatusBadge :status="row[1]" /></p>
            <p v-else class="mt-2 text-sm text-slate-900">{{ row[1] }}</p>
          </div>
        </div>
      </Card>

      <Card class="p-5">
        <p class="text-sm font-semibold text-slate-900">Catatan</p>
        <p class="mt-2 text-sm text-slate-600">{{ donation.notes || '-' }}</p>
      </Card>

      <Card v-if="donation.has_finance_link" class="border-blue-200 bg-blue-50/80 p-5">
        <p class="text-sm font-semibold text-blue-900">Tautan Keuangan</p>
        <p class="mt-2 text-sm text-blue-700">
          Donasi ini terhubung dengan transaksi kas masuk. 
          <Link :href="route('finance.show', donation.finance_transaction_id)" class="font-medium underline hover:text-blue-900">
            Lihat Transaksi
          </Link>
        </p>
      </Card>
    </div>

    <!-- Modals -->
    <ConfirmDialog
      v-model:open="confirmAction"
      title="Konfirmasi Donasi"
      description="Donasi akan diubah statusnya menjadi Dikonfirmasi dan akan dicatat otomatis ke kas masuk."
      confirm-text="Konfirmasi"
      :processing="actionForm.processing"
      @confirm="submitConfirm"
    />

    <ConfirmDialog
      v-model:open="rejectAction"
      title="Tolak Donasi"
      description="Donasi ini akan ditolak. Jika sebelumnya ada catatan kas masuk, maka kas masuk tersebut akan dihapus."
      confirm-text="Tolak"
      :danger="true"
      :processing="actionForm.processing"
      @confirm="submitReject"
    />
  </AuthenticatedLayout>
</template>
