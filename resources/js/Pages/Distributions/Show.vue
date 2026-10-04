<script setup>
import { ref } from "vue"
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import Card from "@/Components/ui/card/Card.vue"
import Button from "@/Components/ui/button/Button.vue"
import Input from "@/Components/ui/input/Input.vue"
import Select from "@/Components/ui/select/Select.vue"
import Badge from "@/Components/ui/badge/Badge.vue"
import { Head, Link, useForm } from "@inertiajs/vue3"

const props = defineProps({ distribution: Object, slaughterings: Array })

const editing = ref(false)
const form = useForm({
  slaughtering_id: props.distribution.slaughtering_id ?? "",
  recipient_name: props.distribution.recipient_name,
  recipient_type: props.distribution.recipient_type ?? "mustahik",
  package_count: props.distribution.package_count ?? 1,
  status: props.distribution.status ?? "pending",
})
const submitEdit = () => form.put(route("distributions.update", props.distribution.id), { onSuccess: () => { editing.value = false } })
const destroy = () => { if (confirm("Hapus distribusi ini?")) useForm({}).delete(route("distributions.destroy", props.distribution.id)) }
const markDelivered = () => useForm({}).post(route("distributions.mark-delivered", props.distribution.id))

const statusVariant = (s) => s === "delivered" ? "success" : "warning"
const distStatusVariant = (s) => s === "completed" ? "success" : s === "in_progress" ? "warning" : "default"
const slaughteringLabel = (s) => {
  const a = s.animal
  return `#${s.id} — ${s.date} — ${a ? `${a.type} #${a.id} (${a.weight}kg)` : "?"}`
}
</script>

<template>
  <Head :title="'Distribusi: ' + distribution.recipient_name" />
  <AuthenticatedLayout :title="'Distribusi: ' + distribution.recipient_name">

    <div class="mb-4 flex items-center gap-3">
      <Link :href="route('distributions.index')"><Button variant="ghost">&larr; Kembali</Button></Link>
      <Button v-if="!editing" size="sm" @click="editing = true">Edit</Button>
      <Button v-else size="sm" variant="outline" @click="editing = false">Batal</Button>
      <Button size="sm" variant="danger" @click="destroy">Hapus</Button>
      <Button v-if="distribution.status === 'pending'" size="sm" variant="success" @click="markDelivered">Tandai Terkirim</Button>
    </div>

    <Card v-if="!editing" class="p-6">
      <div class="mb-4 flex items-center gap-2">
        <Badge :variant="statusVariant(distribution.status)">{{ distribution.status }}</Badge>
        <Badge size="sm">{{ distribution.recipient_type }}</Badge>
      </div>
      <div class="grid gap-4 text-sm md:grid-cols-3">
        <div><p class="text-slate-500">Penerima</p><p class="font-medium">{{ distribution.recipient_name }}</p></div>
        <div><p class="text-slate-500">Jumlah Paket</p><p class="font-medium">{{ distribution.package_count }} paket</p></div>
        <div><p class="text-slate-500">Waktu Kirim</p><p class="font-medium">{{ distribution.delivered_at ?? "—" }}</p></div>
        <div>
          <p class="text-slate-500">Penyembelihan</p>
          <template v-if="distribution.slaughtering">
            <p class="font-medium">#{{ distribution.slaughtering.id }} — {{ distribution.slaughtering.date }}</p>
            <p class="text-xs text-slate-500">{{ distribution.slaughtering.animal?.type }} · {{ distribution.slaughtering.animal?.weight }}kg</p>
            <Badge size="sm" :variant="distStatusVariant(distribution.slaughtering.distribution_status)">{{ distribution.slaughtering.distribution_status }}</Badge>
          </template>
          <span v-else class="text-slate-400">—</span>
        </div>
      </div>
    </Card>

    <Card v-else class="p-6">
      <p class="mb-4 text-sm font-semibold text-slate-800">Edit Distribusi #{{ distribution.id }}</p>
      <div class="grid gap-4 md:grid-cols-3">
        <div>
          <label class="mb-1 block text-xs font-medium text-slate-600">Penyembelihan</label>
          <Select v-model="form.slaughtering_id">
            <option value="">— Pilih —</option>
            <option v-for="s in slaughterings" :key="s.id" :value="s.id">{{ slaughteringLabel(s) }}</option>
          </Select>
        </div>
        <div>
          <label class="mb-1 block text-xs font-medium text-slate-600">Nama Penerima</label>
          <Input v-model="form.recipient_name" />
        </div>
        <div>
          <label class="mb-1 block text-xs font-medium text-slate-600">Tipe</label>
          <Select v-model="form.recipient_type">
            <option value="mustahik">mustahik</option>
            <option value="penerima">penerima</option>
          </Select>
        </div>
        <div>
          <label class="mb-1 block text-xs font-medium text-slate-600">Jumlah Paket</label>
          <Input v-model="form.package_count" type="number" min="1" />
        </div>
        <div>
          <label class="mb-1 block text-xs font-medium text-slate-600">Status</label>
          <Select v-model="form.status">
            <option value="pending">Pending</option>
            <option value="delivered">Delivered</option>
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
