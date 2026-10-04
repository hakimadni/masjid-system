<script setup>
import { Head, Link } from "@inertiajs/vue3"
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import StatCard from "@/Components/qurban/StatCard.vue"
import Card from "@/Components/ui/card/Card.vue"
import Button from "@/Components/ui/button/Button.vue"
import DataTable from "@/Components/ui/table/DataTable.vue"
import EmptyState from "@/Components/ui/empty/EmptyState.vue"
import StatusBadge from "@/Components/ui/status/StatusBadge.vue"
import MoneyDisplay from "@/Components/ui/display/MoneyDisplay.vue"
import PageHeader from "@/Components/ui/page/PageHeader.vue"

const props = defineProps({
  financeEntries: { type: Object, required: true },
  summary: { type: Object, required: true },
})
</script>

<template>
  <Head title="Pending Approval Keuangan" />

  <AuthenticatedLayout title="Pending Approval Keuangan">
    <div class="space-y-6">
      <PageHeader title="Pending Approval Keuangan" description="Fokus pada transaksi draft dan pending yang masih menunggu validasi.">
        <template #actions>
          <Link :href="route('finance.index')"><Button variant="outline">Semua Transaksi</Button></Link>
        </template>
      </PageHeader>

      <div class="grid gap-4 md:grid-cols-3">
        <StatCard title="Draft" :value="summary.draft_total" />
        <StatCard title="Pending" :value="summary.pending_total" />
        <StatCard title="Nominal Menunggu">
          <template #value><MoneyDisplay :value="summary.approval_amount_total" /></template>
        </StatCard>
      </div>

      <Card class="p-0">
        <div class="border-b border-slate-200 px-5 py-4"><p class="text-sm font-semibold text-slate-900">Daftar Menunggu Approval</p></div>
        <div class="p-5">
          <DataTable v-if="financeEntries.data.length">
            <thead class="bg-slate-50 text-xs uppercase text-slate-500">
              <tr>
                <th class="px-4 py-3 text-left">Transaksi</th>
                <th class="px-4 py-3 text-left">Kategori</th>
                <th class="px-4 py-3 text-left">Nominal</th>
                <th class="px-4 py-3 text-left">Status</th>
                <th class="px-4 py-3 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in financeEntries.data" :key="item.id" class="border-t border-slate-100">
                <td class="px-4 py-3"><p class="font-medium text-slate-900">{{ item.title }}</p><p class="text-xs text-slate-500">{{ item.reference_no }} • {{ item.transaction_date }}</p></td>
                <td class="px-4 py-3 text-sm text-slate-600">{{ item.category }}</td>
                <td class="px-4 py-3 text-sm text-slate-900"><MoneyDisplay :value="item.amount" /></td>
                <td class="px-4 py-3"><StatusBadge :status="item.status" /></td>
                <td class="px-4 py-3 text-right"><Link :href="route('finance.show', item.id)"><Button size="sm" variant="outline">Detail</Button></Link></td>
              </tr>
            </tbody>
          </DataTable>
          <EmptyState v-else title="Tidak ada transaksi menunggu approval" description="Semua transaksi draft/pending sudah selesai diproses." />
        </div>
      </Card>
    </div>
  </AuthenticatedLayout>
</template>
