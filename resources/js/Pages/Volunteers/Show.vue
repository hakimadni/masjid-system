<script setup>
import { ref } from "vue"
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import Card from "@/Components/ui/card/Card.vue"
import Button from "@/Components/ui/button/Button.vue"
import Input from "@/Components/ui/input/Input.vue"
import Select from "@/Components/ui/select/Select.vue"
import Badge from "@/Components/ui/badge/Badge.vue"
import QrCard from "@/Components/qurban/QrCard.vue"
import { Head, Link, useForm } from "@inertiajs/vue3"

const props = defineProps({ volunteer: Object })

const editing = ref(false)
const form = useForm({
  name: props.volunteer.name,
  phone: props.volunteer.phone ?? "",
  role_type: props.volunteer.role_type ?? "administrasi",
})
const submitEdit = () => form.put(route("volunteers.update", props.volunteer.id), { onSuccess: () => { editing.value = false } })
const destroy = () => { if (confirm("Hapus relawan ini?")) useForm({}).delete(route("volunteers.destroy", props.volunteer.id)) }
</script>

<template>
  <Head :title="'Relawan: ' + volunteer.name" />
  <AuthenticatedLayout :title="'Relawan: ' + volunteer.name">

    <div class="mb-4 flex items-center gap-3">
      <Link :href="route('volunteers.index')"><Button variant="ghost">&larr; Kembali</Button></Link>
      <Button v-if="!editing" size="sm" @click="editing = true">Edit</Button>
      <Button v-else size="sm" variant="outline" @click="editing = false">Batal</Button>
      <Button size="sm" variant="danger" @click="destroy">Hapus</Button>
      <a :href="route('volunteers.id-card-pdf', volunteer.id)"><Button size="sm" variant="outline">Print ID Card</Button></a>
    </div>

    <Card v-if="!editing" class="p-6">
      <div class="mb-4 flex items-center gap-2">
        <Badge>{{ volunteer.role_type }}</Badge>
      </div>
      <div class="grid gap-4 text-sm md:grid-cols-2">
        <div><p class="text-slate-500">Nama</p><p class="font-medium">{{ volunteer.name }}</p></div>
        <div><p class="text-slate-500">Telepon</p><p class="font-medium">{{ volunteer.phone }}</p></div>
        <div><p class="text-slate-500">Role</p><p class="font-medium">{{ volunteer.role_type }}</p></div>
        <div>
          <p class="text-slate-500 mb-1">QR Token</p>
          <QrCard label="QR Token" :token="volunteer.qr_token" />
        </div>
      </div>
    </Card>

    <Card v-else class="p-6">
      <p class="mb-4 text-sm font-semibold text-slate-800">Edit Relawan #{{ volunteer.id }}</p>
      <div class="grid gap-4 md:grid-cols-2">
        <div>
          <label class="mb-1 block text-xs font-medium text-slate-600">Nama</label>
          <Input v-model="form.name" />
        </div>
        <div>
          <label class="mb-1 block text-xs font-medium text-slate-600">Telepon</label>
          <Input v-model="form.phone" />
        </div>
        <div>
          <label class="mb-1 block text-xs font-medium text-slate-600">Role</label>
          <Select v-model="form.role_type">
            <option value="administrasi">administrasi</option>
            <option value="penyembelih">penyembelih</option>
            <option value="distribusi">distribusi</option>
            <option value="dokumentasi">dokumentasi</option>
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
