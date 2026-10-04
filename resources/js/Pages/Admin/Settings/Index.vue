<script setup>
import { computed } from "vue"
import { Head, useForm, usePage } from "@inertiajs/vue3"
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import Card from "@/Components/ui/card/Card.vue"
import CardHeader from "@/Components/ui/card/CardHeader.vue"
import CardTitle from "@/Components/ui/card/CardTitle.vue"
import CardContent from "@/Components/ui/card/CardContent.vue"
import Button from "@/Components/ui/button/Button.vue"
import Input from "@/Components/ui/input/Input.vue"
import Textarea from "@/Components/ui/textarea/Textarea.vue"

const props = defineProps({
  mosque: { type: Object, required: true },
  accounts: { type: Array, default: () => [] },
  settings: { type: Object, default: () => ({}) },
})

const form = useForm({
  name: props.mosque?.name ?? "",
  address: props.mosque?.address ?? "",
  phone: props.mosque?.phone ?? "",
  email: props.mosque?.email ?? "",
  description: props.mosque?.description ?? "",
  settings: {
    show_public_report: props.settings?.show_public_report ?? false,
    show_donation_page: props.settings?.show_donation_page ?? true,
    show_events: props.settings?.show_events ?? true,
    show_announcements: props.settings?.show_announcements ?? true,
    show_prayer_schedule: props.settings?.show_prayer_schedule ?? true,
    show_contact: props.settings?.show_contact ?? true,
  },
})

const submit = () => {
  form.put(route("settings.update"))
}
</script>

<template>
  <Head title="Pengaturan Masjid" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-slate-900">Pengaturan Masjid</h2>
    </template>

    <div class="py-6">
      <div class="mx-auto max-w-3xl space-y-6">
        <Card>
          <CardHeader>
            <CardTitle>Profil Masjid</CardTitle>
          </CardHeader>
          <CardContent>
            <form @submit.prevent="submit" class="space-y-4">
              <div>
                <label class="block text-sm font-medium mb-1">Nama Masjid</label>
                <Input v-model="form.name" required />
              </div>

              <div>
                <label class="block text-sm font-medium mb-1">Alamat</label>
                <Textarea v-model="form.address" rows="2" />
              </div>

              <div class="grid md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium mb-1">Telepon</label>
                  <Input v-model="form.phone" />
                </div>
                <div>
                  <label class="block text-sm font-medium mb-1">Email</label>
                  <Input v-model="form.email" type="email" />
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium mb-1">Deskripsi</label>
                <Textarea v-model="form.description" rows="3" />
              </div>

              <Button type="submit" :disabled="form.processing">Simpan Perubahan</Button>
            </form>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle>Pengaturan Portal Publik</CardTitle>
          </CardHeader>
          <CardContent class="space-y-3">
            <label class="flex items-center gap-2">
              <input type="checkbox" v-model="form.settings.show_public_report" />
              <span>Tampilkan laporan keuangan publik</span>
            </label>
            <label class="flex items-center gap-2">
              <input type="checkbox" v-model="form.settings.show_donation_page" />
              <span>Tampilkan halaman donasi</span>
            </label>
            <label class="flex items-center gap-2">
              <input type="checkbox" v-model="form.settings.show_events" />
              <span>Tampilkan acara/kajian</span>
            </label>
            <label class="flex items-center gap-2">
              <input type="checkbox" v-model="form.settings.show_announcements" />
              <span>Tampilkan pengumuman</span>
            </label>
            <label class="flex items-center gap-2">
              <input type="checkbox" v-model="form.settings.show_prayer_schedule" />
              <span>Tampilkan jadwal shalat</span>
            </label>
            <label class="flex items-center gap-2">
              <input type="checkbox" v-model="form.settings.show_contact" />
              <span>Tampilkan kontak masjid</span>
            </label>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle>Rekening Donasi</CardTitle>
          </CardHeader>
          <CardContent>
            <p class="text-sm text-slate-500 mb-2">Kelola rekening donasi di halaman Keuangan &gt; Akun.</p>
            <ul class="text-sm space-y-1">
              <li v-for="account in accounts" :key="account.id">
                {{ account.bank_name }} - {{ account.account_number }} ({{ account.account_holder }})
              </li>
              <li v-if="!accounts.length">Belum ada rekening donasi yang ditambahkan.</li>
            </ul>
          </CardContent>
        </Card>
      </div>
    </div>
  </AuthenticatedLayout>
</template>