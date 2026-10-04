<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import DataTable from "@/Components/ui/table/DataTable.vue"
import QrCard from "@/Components/qurban/QrCard.vue"
import Card from "@/Components/ui/card/Card.vue"
import Button from "@/Components/ui/button/Button.vue"
import Input from "@/Components/ui/input/Input.vue"
import Select from "@/Components/ui/select/Select.vue"
import { Head, Link, useForm } from "@inertiajs/vue3"

defineProps({ volunteers: Object })

const form = useForm({ name: "", phone: "", role_type: "administrasi" })
const submit = () => form.post(route("volunteers.store"))
</script>

<template>
  <Head title="Relawan Qurban" />
  <AuthenticatedLayout title="Relawan Qurban">

    <div class="space-y-6">
      <Card class="p-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
          <p class="text-sm font-medium text-slate-700">Tambah Relawan</p>
          <a :href="route('volunteers.id-card-batch-pdf')"><Button variant="outline">Batch Print ID Card</Button></a>
        </div>

        <div class="mt-4 grid gap-3 md:grid-cols-4">
          <Input v-model="form.name" placeholder="Nama" />
          <Input v-model="form.phone" placeholder="Telepon" />
          <Select v-model="form.role_type">
            <option value="administrasi">administrasi</option>
            <option value="penyembelih">penyembelih</option>
            <option value="distribusi">distribusi</option>
            <option value="dokumentasi">dokumentasi</option>
          </Select>
          <Button @click="submit">Simpan</Button>
        </div>
      </Card>

      <DataTable>
        <thead class="bg-slate-50 text-xs uppercase text-slate-500">
          <tr>
            <th class="px-4 py-3 text-left">Nama</th>
            <th class="px-4 py-3 text-left">Role</th>
            <th class="px-4 py-3 text-left">QR</th>
            <th class="px-4 py-3 text-left">ID Card</th>
            <th class="px-4 py-3 text-left"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in volunteers.data" :key="item.id" class="border-t border-slate-100 align-top hover:bg-slate-50/80">
            <td class="px-4 py-3">{{ item.name }}</td>
            <td class="px-4 py-3">{{ item.role_type }}</td>
            <td class="px-4 py-3"><QrCard label="QR Token" :token="item.qr_token" /></td>
            <td class="px-4 py-3">
              <a :href="route('volunteers.id-card-pdf', item.id)"><Button variant="outline" size="sm">Print</Button></a>
            </td>
            <td class="px-4 py-3">
              <Link :href="route('volunteers.show', item.id)">
                <Button size="sm" variant="outline">Detail</Button>
              </Link>
            </td>
          </tr>
        </tbody>
      </DataTable>
    </div>
  </AuthenticatedLayout>
</template>
