<script setup>
import { computed, ref } from "vue"
import { Head, useForm, usePage } from "@inertiajs/vue3"
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import Card from "@/Components/ui/card/Card.vue"
import Button from "@/Components/ui/button/Button.vue"
import Input from "@/Components/ui/input/Input.vue"
import ConfirmDialog from "@/Components/ui/dialog/ConfirmDialog.vue"
import PageHeader from "@/Components/ui/page/PageHeader.vue"
import StatusBadge from "@/Components/ui/status/StatusBadge.vue"

defineProps({
  categories: { type: Array, required: true },
})

const page = usePage()
const flash = computed(() => page.props.flash ?? {})

const confirmDelete = ref(false)
const selectedCategory = ref(null)

const form = useForm({
  name: "",
  description: "",
  is_active: true,
})

const submitCreate = () => {
  form.post(route("donation-categories.store"), {
    preserveScroll: true,
    onSuccess: () => form.reset(),
  })
}

const openDelete = (category) => {
  selectedCategory.value = category
  confirmDelete.value = true
}

const submitDelete = () => {
  if (!selectedCategory.value) return
  useForm().delete(route("donation-categories.destroy", selectedCategory.value.id), {
    preserveScroll: true,
    onSuccess: () => {
      confirmDelete.value = false
      selectedCategory.value = null
    },
  })
}
</script>

<template>
  <Head title="Kategori Donasi" />
  <AuthenticatedLayout title="Kategori Donasi">
    <div class="space-y-6">
      <PageHeader
        title="Kategori Donasi"
        description="Kelola daftar program donasi yang tersedia di form pencatatan."
      />

      <div v-if="flash.success" class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
        {{ flash.success }}
      </div>

      <Card class="p-5">
        <p class="text-sm font-semibold text-slate-900">Tambah Kategori Baru</p>
        <form class="mt-4 grid gap-3 md:grid-cols-2" @submit.prevent="submitCreate">
          <div class="md:col-span-2">
            <label class="mb-1.5 block text-sm font-medium text-slate-900">Nama Kategori</label>
            <Input v-model="form.name" placeholder="Infaq Harian / Zakat Fitrah" />
            <p v-if="$page.props.errors.name" class="mt-1 text-xs text-rose-600">{{ $page.props.errors.name }}</p>
          </div>
          <div class="md:col-span-2">
            <label class="mb-1.5 block text-sm font-medium text-slate-900">Deskripsi Singkat</label>
            <Input v-model="form.description" placeholder="Opsional: konteks penggunaan kategori." />
            <p v-if="$page.props.errors.description" class="mt-1 text-xs text-rose-600">{{ $page.props.errors.description }}</p>
          </div>
          <div class="md:col-span-2 flex justify-end">
            <Button :disabled="form.processing">
              {{ form.processing ? "Menyimpan..." : "Simpan Kategori" }}
            </Button>
          </div>
        </form>
      </Card>

      <Card class="divide-y divide-slate-100">
        <div class="border-b border-slate-200 px-5 py-4">
          <p class="text-sm font-semibold text-slate-900">Daftar Kategori</p>
        </div>
        <div v-if="categories.length" class="divide-y divide-slate-100">
          <div v-for="category in categories" :key="category.id" class="flex flex-wrap items-center justify-between gap-3 px-5 py-3">
            <div>
              <p class="font-medium text-slate-900">{{ category.name }}</p>
              <p class="text-xs text-slate-500">{{ category.description ?? "Tanpa deskripsi" }}</p>
            </div>
            <div class="flex items-center gap-3">
              <StatusBadge :status="category.is_active ? 'active' : 'draft'" />
              <Button variant="outline" size="sm" @click="openDelete(category)">Hapus</Button>
            </div>
          </div>
        </div>
        <div v-else class="px-5 py-10 text-center text-sm text-slate-500">Belum ada kategori donasi.</div>
      </Card>
    </div>

    <ConfirmDialog
      v-if="selectedCategory"
      v-model:open="confirmDelete"
      title="Hapus kategori donasi"
      :description="`Kategori ${selectedCategory.name} akan dihapus. Data donasi yang sudah tercatat tidak akan ikut terhapus.`"
      confirm-text="Hapus"
      :danger="true"
      :processing="form.processing"
      @confirm="submitDelete"
    />
  </AuthenticatedLayout>
</template>
