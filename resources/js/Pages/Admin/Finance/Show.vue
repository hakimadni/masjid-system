<script setup>
import { Head, Link } from "@inertiajs/vue3"
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import Card from "@/Components/ui/card/Card.vue"
import Button from "@/Components/ui/button/Button.vue"
import MoneyDisplay from "@/Components/ui/display/MoneyDisplay.vue"
import StatusBadge from "@/Components/ui/status/StatusBadge.vue"
import PageHeader from "@/Components/ui/page/PageHeader.vue"

const props = defineProps({
  transaction: { type: Object, required: true },
})

const detailRows = [
  ["Referensi", props.transaction.reference_no],
  ["Judul", props.transaction.title],
  ["Jenis", props.transaction.entry_type === "income" ? "Pemasukan" : "Pengeluaran"],
  ["Status", props.transaction.status],
  ["Tanggal", props.transaction.transaction_date],
  ["Kategori", props.transaction.category ?? "-"],
  ["Akun", props.transaction.account ?? "-"],
  ["Tipe Akun", props.transaction.account_type ?? "-"],
  ["Metode", props.transaction.payment_method],
  ["Dibuat Oleh", props.transaction.creator ?? "-"],
  ["Disetujui Oleh", props.transaction.approver ?? "-"],
  ["Waktu Approve", props.transaction.approved_at ?? "-"],
]
</script>

<template>
  <Head :title="`Detail ${transaction.reference_no}`" />

  <AuthenticatedLayout title="Detail Keuangan">
    <div class="space-y-6">
      <PageHeader :title="transaction.title" :description="`Referensi ${transaction.reference_no}`">
        <template #actions>
          <Link :href="route('finance.index')"><Button variant="outline">Kembali</Button></Link>
          <Link v-if="transaction.can_edit" :href="route('finance.edit', transaction.id)"><Button>Edit</Button></Link>
        </template>
      </PageHeader>

      <Card class="p-5">
        <div class="grid gap-4 md:grid-cols-2">
          <div>
            <p class="text-sm text-slate-500">Nominal</p>
            <p class="mt-1 text-2xl font-semibold text-slate-900"><MoneyDisplay :value="transaction.amount" /></p>
          </div>
          <div>
            <p class="text-sm text-slate-500">Status</p>
            <div class="mt-1"><StatusBadge :status="transaction.status" /></div>
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
        <p class="mt-2 text-sm text-slate-600">{{ transaction.notes || '-' }}</p>
      </Card>

      <Card v-if="transaction.rejected_reason" class="border-rose-200 bg-rose-50/80 p-5">
        <p class="text-sm font-semibold text-rose-900">Alasan Penolakan</p>
        <p class="mt-2 text-sm text-rose-700">{{ transaction.rejected_reason }}</p>
      </Card>
    </div>
  </AuthenticatedLayout>
</template>
