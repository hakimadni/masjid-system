<script setup>
import { computed, ref } from "vue"
import { Head, useForm, usePage, router, Link } from "@inertiajs/vue3"
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import Card from "@/Components/ui/card/Card.vue"
import Button from "@/Components/ui/button/Button.vue"
import ConfirmDialog from "@/Components/ui/dialog/ConfirmDialog.vue"
import Dialog from "@/Components/ui/dialog/Dialog.vue"
import DataTable from "@/Components/ui/table/DataTable.vue"
import EmptyState from "@/Components/ui/empty/EmptyState.vue"
import Input from "@/Components/ui/input/Input.vue"
import PageHeader from "@/Components/ui/page/PageHeader.vue"
import Select from "@/Components/ui/select/Select.vue"
import StatusBadge from "@/Components/ui/status/StatusBadge.vue"
import TableSkeleton from "@/Components/ui/table/TableSkeleton.vue"

const props = defineProps({
  jamaahs: { type: Object, required: true },
  filters: { type: Object, required: true },
})

const page = usePage()
const flash = computed(() => page.props.flash ?? {})

const confirmDelete = ref(false)
const selectedJamaah = ref(null)

const showForm = ref(false)
const isEditing = ref(false)

const filterForm = useForm({
  search: props.filters.search ?? "",
  status: props.filters.status ?? "",
  category: props.filters.category ?? "",
})

const applyFilters = () => {
  router.get(route("jamaahs.index", filterForm.data()), {
    preserveState: true,
    replace: true,
    preserveScroll: true,
  })
}

const resetFilters = () => {
  filterForm.set("search", "")
  filterForm.set("status", "")
  filterForm.set("category", "")
  applyFilters()
}

const openDelete = (jamaah) => {
  selectedJamaah.value = jamaah
  confirmDelete.value = true
}

const submitDelete = () => {
  if (!selectedJamaah.value) return
  useForm().delete(route("jamaahs.destroy", selectedJamaah.value.id), {
    preserveScroll: true,
    onSuccess: () => {
      confirmDelete.value = false
      selectedJamaah.value = null
      applyFilters()
    },
  })
}

const jamaahForm = useForm({
  name: "",
  gender: "",
  category: "umum",
  phone: "",
  email: "",
  address: "",
  birth_date: "",
  family_role: "",
  status: "active",
  notes: "",
})

const openCreate = () => {
  isEditing.value = false
  jamaahForm.reset()
  showForm.value = true
}

const openEdit = (jamaah) => {
  isEditing.value = true
  selectedJamaah.value = jamaah
  jamaahForm.name = jamaah.name
  jamaahForm.gender = jamaah.gender || ""
  jamaahForm.category = jamaah.category || "umum"
  jamaahForm.phone = jamaah.is_masked ? "" : (jamaah.phone || "")
  jamaahForm.email = jamaah.is_masked ? "" : (jamaah.email || "")
  jamaahForm.address = jamaah.is_masked ? "" : (jamaah.address || "")
  jamaahForm.birth_date = jamaah.birth_date || ""
  jamaahForm.family_role = jamaah.family_role || ""
  jamaahForm.status = jamaah.status || "active"
  jamaahForm.notes = jamaah.notes || ""
  showForm.value = true
}

const submitForm = () => {
  if (isEditing.value) {
    jamaahForm.put(route("jamaahs.update", selectedJamaah.value.id), {
      preserveScroll: true,
      onSuccess: () => {
        showForm.value = false
        applyFilters()
      },
    })
  } else {
    jamaahForm.post(route("jamaahs.store"), {
      preserveScroll: true,
      onSuccess: () => {
        showForm.value = false
        applyFilters()
      },
    })
  }
}
</script>

<template>
  <Head title="Data Jamaah" />
  <AuthenticatedLayout title="Data Jamaah">
    <div class="space-y-6">
      <PageHeader
        title="Data Jamaah"
        description="Kelola data jamaah untuk segmentasi komunitas dan kehadiran kegiatan."
      >
        <template #actions>
          <Button @click="openCreate">Tambah Jamaah</Button>
        </template>
      </PageHeader>

      <div v-if="flash.success" class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
        {{ flash.success }}
      </div>

      <Card class="p-5">
        <p class="text-sm font-semibold text-slate-900">Filter Data Jamaah</p>
        <div class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-5">
          <Input v-model="filterForm.search" placeholder="Cari nama / telepon / email" />
          <Select v-model="filterForm.category">
            <option value="">Semua Kategori</option>
            <option value="umum">Umum</option>
            <option value="pengurus">Pengurus</option>
            <option value="relawan">Relawan</option>
            <option value="mustahik">Mustahik</option>
            <option value="donatur">Donatur</option>
          </Select>
          <Select v-model="filterForm.status">
            <option value="">Semua Status</option>
            <option value="active">Aktif</option>
            <option value="inactive">Tidak Aktif</option>
            <option value="deceased">Wafat</option>
            <option value="moved">Pindah</option>
          </Select>
          <Button :disabled="filterForm.processing" @click="applyFilters">
            {{ filterForm.processing ? "Memuat..." : "Terapkan" }}
          </Button>
          <Button variant="outline" @click="resetFilters">Reset</Button>
        </div>
      </Card>

      <Card class="p-0">
        <div class="border-b border-slate-200 px-5 py-4">
          <p class="text-sm font-semibold text-slate-900">Daftar Jamaah</p>
        </div>
        <div class="p-5">
          <TableSkeleton v-if="router.isLoading" :rows="6" :columns="8" />
          <template v-else>
            <DataTable v-if="props.jamaahs.data.length">
              <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                  <th class="px-4 py-3 text-left">Nama</th>
                  <th class="px-4 py-3 text-left">Kategori</th>
                  <th class="px-4 py-3 text-left">Telepon</th>
                  <th class="px-4 py-3 text-left">Email</th>
                  <th class="px-4 py-3 text-left">Status</th>
                  <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="jamaah in props.jamaahs.data" :key="jamaah.id" class="border-t border-slate-100">
                  <td class="px-4 py-3">
                    <p class="font-medium text-slate-900">{{ jamaah.name }}</p>
                    <p class="text-xs text-slate-500">{{ jamaah.family_role ?? "-" }}</p>
                  </td>
                  <td class="px-4 py-3">
                    <p class="text-sm capitalize">{{ jamaah.category ?? "-" }}</p>
                  </td>
                  <td class="px-4 py-3">
                    <p class="text-sm">{{ jamaah.phone ?? "-" }}</p>
                  </td>
                  <td class="px-4 py-3">
                    <p class="text-sm">{{ jamaah.email ?? "-" }}</p>
                  </td>
                  <td class="px-4 py-3"><StatusBadge :status="jamaah.status" /></td>
                  <td class="px-4 py-3 text-right">
                    <div class="flex justify-end gap-2">
                      <Link :href="route('jamaahs.show', jamaah.id)"><Button variant="outline" size="sm">Detail</Button></Link>
                      <Button variant="outline" size="sm" @click="openEdit(jamaah)">Edit</Button>
                      <Button variant="outline" size="sm" @click="openDelete(jamaah)">Hapus</Button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </DataTable>
            <EmptyState v-else title="Belum ada jamaah" description="Data jamaah akan muncul setelah data pertama ditambahkan." />
          </template>
        </div>
      </Card>

      <div class="mt-4 flex items-center justify-between text-sm text-slate-500">
        <p>Menampilkan {{ props.jamaahs.data.length }} dari total {{ props.jamaahs.total }} jamaah.</p>
        <div class="flex gap-2">
          <Button
            size="sm"
            variant="outline"
            :disabled="!props.jamaahs.prev_page_url"
            @click="router.get(props.jamaahs.prev_page_url, { preserveState: true, replace: true, preserveScroll: true })"
          >
            Sebelumnya
          </Button>
          <Button
            size="sm"
            variant="outline"
            :disabled="!props.jamaahs.next_page_url"
            @click="router.get(props.jamaahs.next_page_url, { preserveState: true, replace: true, preserveScroll: true })"
          >
            Berikutnya
          </Button>
        </div>
      </div>
    </div>

    <!-- Modals -->
    <Dialog v-model:open="showForm" :title="isEditing ? 'Edit Jamaah' : 'Tambah Jamaah'">
      <form @submit.prevent="submitForm" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-slate-700">Nama Lengkap *</label>
          <Input v-model="jamaahForm.name" class="mt-1" required />
          <p v-if="jamaahForm.errors.name" class="mt-1 text-sm text-red-600">{{ jamaahForm.errors.name }}</p>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-slate-700">Jenis Kelamin</label>
            <Select v-model="jamaahForm.gender" class="mt-1">
              <option value="">Pilih</option>
              <option value="male">Laki-laki</option>
              <option value="female">Perempuan</option>
            </Select>
            <p v-if="jamaahForm.errors.gender" class="mt-1 text-sm text-red-600">{{ jamaahForm.errors.gender }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700">Kategori *</label>
            <Select v-model="jamaahForm.category" class="mt-1" required>
              <option value="umum">Umum</option>
              <option value="pengurus">Pengurus</option>
              <option value="relawan">Relawan</option>
              <option value="mustahik">Mustahik</option>
              <option value="donatur">Donatur</option>
            </Select>
            <p v-if="jamaahForm.errors.category" class="mt-1 text-sm text-red-600">{{ jamaahForm.errors.category }}</p>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-slate-700">Nomor Telepon</label>
            <Input v-model="jamaahForm.phone" class="mt-1" />
            <p v-if="jamaahForm.errors.phone" class="mt-1 text-sm text-red-600">{{ jamaahForm.errors.phone }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700">Email</label>
            <Input v-model="jamaahForm.email" type="email" class="mt-1" />
            <p v-if="jamaahForm.errors.email" class="mt-1 text-sm text-red-600">{{ jamaahForm.errors.email }}</p>
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700">Alamat</label>
          <Input v-model="jamaahForm.address" class="mt-1" />
          <p v-if="jamaahForm.errors.address" class="mt-1 text-sm text-red-600">{{ jamaahForm.errors.address }}</p>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-slate-700">Tanggal Lahir</label>
            <Input v-model="jamaahForm.birth_date" type="date" class="mt-1" />
            <p v-if="jamaahForm.errors.birth_date" class="mt-1 text-sm text-red-600">{{ jamaahForm.errors.birth_date }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700">Peran Keluarga</label>
            <Input v-model="jamaahForm.family_role" placeholder="Kepala Keluarga / Anak / Istri" class="mt-1" />
            <p v-if="jamaahForm.errors.family_role" class="mt-1 text-sm text-red-600">{{ jamaahForm.errors.family_role }}</p>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-slate-700">Status *</label>
            <Select v-model="jamaahForm.status" class="mt-1" required>
              <option value="active">Aktif</option>
              <option value="inactive">Tidak Aktif</option>
              <option value="deceased">Wafat</option>
              <option value="moved">Pindah</option>
            </Select>
            <p v-if="jamaahForm.errors.status" class="mt-1 text-sm text-red-600">{{ jamaahForm.errors.status }}</p>
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700">Catatan</label>
          <Input v-model="jamaahForm.notes" class="mt-1" />
          <p v-if="jamaahForm.errors.notes" class="mt-1 text-sm text-red-600">{{ jamaahForm.errors.notes }}</p>
        </div>

        <div class="flex justify-end gap-2 pt-4">
          <Button type="button" variant="outline" @click="showForm = false">Batal</Button>
          <Button type="submit" :disabled="jamaahForm.processing">Simpan</Button>
        </div>
      </form>
    </Dialog>

    <ConfirmDialog
      v-if="selectedJamaah"
      v-model:open="confirmDelete"
      title="Hapus data jamaah"
      :description="`Jamaah ${selectedJamaah.name} akan dihapus secara permanen.`"
      confirm-text="Hapus"
      :danger="true"
      @confirm="submitDelete"
    />
  </AuthenticatedLayout>
</template>
