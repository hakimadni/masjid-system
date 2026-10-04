<script setup>
import { computed } from "vue"
import { Head, Link, useForm, usePage } from "@inertiajs/vue3"
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import Card from "@/Components/ui/card/Card.vue"
import Button from "@/Components/ui/button/Button.vue"
import Input from "@/Components/ui/input/Input.vue"
import Select from "@/Components/ui/select/Select.vue"
import Textarea from "@/Components/ui/textarea/Textarea.vue"
import PageHeader from "@/Components/ui/page/PageHeader.vue"

const props = defineProps({
  transaction: { type: Object, required: true },
  accounts: { type: Array, required: true },
  categories: { type: Array, required: true },
})

const page = usePage()
const errors = computed(() => page.props.errors ?? {})

const form = useForm({
  title: props.transaction.title,
  entry_type: props.transaction.entry_type,
  finance_account_id: props.transaction.finance_account_id ?? "",
  finance_category_id: props.transaction.finance_category_id ?? "",
  amount: props.transaction.amount,
  payment_method: props.transaction.payment_method,
  status: props.transaction.status,
  transaction_date: props.transaction.transaction_date,
  notes: props.transaction.notes ?? "",
})

const filteredCategories = computed(() => props.categories.filter((item) => item.entry_type === form.entry_type))
const submit = () => form.put(route('finance.update', props.transaction.id), { preserveScroll: true })
const errorFor = (key) => errors.value[key] ?? ""
</script>

<template>
  <Head :title="`Edit ${transaction.reference_no}`" />

  <AuthenticatedLayout title="Edit Keuangan">
    <div class="space-y-6">
      <PageHeader :title="`Edit ${transaction.reference_no}`" description="Hanya transaksi draft dan pending yang boleh diubah.">
        <template #actions>
          <Link :href="route('finance.show', transaction.id)"><Button variant="outline">Batal</Button></Link>
        </template>
      </PageHeader>

      <Card class="p-5">
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
          <div class="xl:col-span-2">
            <label class="mb-1.5 block text-sm font-medium text-slate-900">Judul transaksi</label>
            <Input v-model="form.title" />
            <p v-if="errorFor('title')" class="mt-1 text-xs text-rose-600">{{ errorFor('title') }}</p>
          </div>
          <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-900">Jenis</label>
            <Select v-model="form.entry_type">
              <option value="income">Pemasukan</option>
              <option value="expense">Pengeluaran</option>
            </Select>
          </div>
          <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-900">Akun kas</label>
            <Select v-model="form.finance_account_id">
              <option value="">Pilih akun kas</option>
              <option v-for="item in accounts" :key="item.id" :value="item.id">{{ item.name }}</option>
            </Select>
          </div>
          <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-900">Kategori</label>
            <Select v-model="form.finance_category_id">
              <option value="">Pilih kategori</option>
              <option v-for="item in filteredCategories" :key="item.id" :value="item.id">{{ item.name }}</option>
            </Select>
          </div>
          <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-900">Nominal</label>
            <Input v-model="form.amount" inputmode="numeric" />
          </div>
          <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-900">Metode</label>
            <Select v-model="form.payment_method">
              <option value="cash">Tunai</option>
              <option value="transfer">Transfer</option>
              <option value="qris">QRIS</option>
              <option value="other">Lainnya</option>
            </Select>
          </div>
          <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-900">Status</label>
            <Select v-model="form.status">
              <option value="draft">Draft</option>
              <option value="pending">Pending</option>
              <option value="approved">Approved</option>
            </Select>
          </div>
          <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-900">Tanggal transaksi</label>
            <Input v-model="form.transaction_date" type="date" />
          </div>
          <div class="xl:col-span-4">
            <label class="mb-1.5 block text-sm font-medium text-slate-900">Catatan</label>
            <Textarea v-model="form.notes" :rows="4" />
          </div>
        </div>

        <div class="mt-5 flex justify-end">
          <Button :disabled="form.processing" @click="submit">{{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}</Button>
        </div>
      </Card>
    </div>
  </AuthenticatedLayout>
</template>
