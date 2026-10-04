<script setup>
import Dialog from "@/Components/ui/dialog/Dialog.vue"
import MobileFab from "@/Components/MobileFab.vue"
import { computed, ref } from "vue"
import { Head, router, useForm, usePage } from "@inertiajs/vue3"
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import Card from "@/Components/ui/card/Card.vue"
import CardHeader from "@/Components/ui/card/CardHeader.vue"
import CardTitle from "@/Components/ui/card/CardTitle.vue"
import CardContent from "@/Components/ui/card/CardContent.vue"
import Button from "@/Components/ui/button/Button.vue"
import Input from "@/Components/ui/input/Input.vue"
import Select from "@/Components/ui/select/Select.vue"
import Textarea from "@/Components/ui/textarea/Textarea.vue"
import DataTable from "@/Components/ui/table/DataTable.vue"
import Badge from "@/Components/ui/badge/Badge.vue"
import ToastMessage from "@/Components/ui/toast/ToastMessage.vue"
import ActionMenu from "@/Components/ui/dropdown/ActionMenu.vue"
import ConfirmDialog from "@/Components/ui/dialog/ConfirmDialog.vue"
import PageHeader from "@/Components/ui/page/PageHeader.vue"

const props = defineProps({
  documents: Object,
  filters: Object,
  categories: Array,
})

const page = usePage()
const flash = computed(() => page.props.flash ?? {})

const createDialogOpen = ref(false)
const createForm = useForm({
  title: "",
  category: "surat-masuk",
  document_number: "",
  document_date: "",
  description: "",
  file: null,
  status: "draft",
})

const confirmDelete = ref(false)
const selectedDoc = ref(null)
const deleteForm = useForm({})

const submitCreate = () => {
  createForm.post(route("documents.store"), {
    preserveScroll: true,
    onSuccess: () => createForm.reset("title", "category", "document_number", "document_date", "description", "file"),
  })
}

const statusVariant = (status) => {
  if (status === "published") return "success"
  if (status === "archived") return "secondary"
  return "default"
}

const formatFileSize = (bytes) => {
  if (!bytes) return "-"
  const units = ["B", "KB", "MB", "GB"]
  let i = 0
  let size = bytes
  while (size >= 1024 && i < units.length - 1) { size /= 1024; i++ }
  return `${size.toFixed(1)} ${units[i]}`
}

const openDeleteDialog = (doc) => { selectedDoc.value = doc; confirmDelete.value = true }
const closeDeleteDialog = () => { confirmDelete.value = false; selectedDoc.value = null }

const destroyDocument = () => {
  if (!selectedDoc.value) return
  deleteForm.delete(route("documents.destroy", selectedDoc.value.id), {
    preserveScroll: true,
    onSuccess: closeDeleteDialog,
  })
}

const actionItems = (doc) => [
  { key: "detail", label: "Detail" },
  { key: "download", label: "Download", disabled: !doc.file_path },
  { key: "delete", label: "Hapus", tone: "danger" },
]

const handleAction = (doc, key) => {
  if (key === "detail") router.visit(route("documents.show", doc.id))
  if (key === "download") window.open(route("documents.download", doc.id), "_blank")
  if (key === "delete") openDeleteDialog(doc)
}
</script>

<template>
  <Head title="Dokumen" />
  <AuthenticatedLayout title="Dokumen">

    <div class="space-y-6">
      

      <PageHeader title="Dokumen" description="Kelola surat, proposal, LPJ, SK, dan arsip masjid." />

      

      <DataTable>
        <thead class="bg-slate-50 text-xs uppercase text-slate-500">
          <tr>
            <th class="px-4 py-3 text-left">Judul</th>
            <th class="px-4 py-3 text-left">Kategori</th>
            <th class="px-4 py-3 text-left">Nomor</th>
            <th class="px-4 py-3 text-left">Tanggal</th>
            <th class="px-4 py-3 text-left">Status</th>
            <th class="px-4 py-3 text-left">Ukuran</th>
            <th class="px-4 py-3 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="doc in documents.data" :key="doc.id" class="border-t border-slate-100 hover:bg-slate-50/80">
            <td class="px-4 py-3 font-medium">{{ doc.title }}</td>
            <td class="px-4 py-3">{{ doc.category }}</td>
            <td class="px-4 py-3">{{ doc.document_number || '-' }}</td>
            <td class="px-4 py-3">{{ doc.document_date || '-' }}</td>
            <td class="px-4 py-3">
              <Badge :variant="statusVariant(doc.status)">{{ doc.status }}</Badge>
            </td>
            <td class="px-4 py-3">{{ formatFileSize(doc.file_size) }}</td>
            <td class="px-4 py-3 text-right">
              <ActionMenu :items="actionItems(doc)" @select="handleAction(doc, $event)" />
            </td>
          </tr>
        </tbody>
      </DataTable>

      <ConfirmDialog
        :open="confirmDelete"
        title="Hapus Dokumen"
        :description="selectedDoc ? `Hapus dokumen ${selectedDoc.title}? Tindakan ini tidak dapat dibatalkan.` : 'Hapus dokumen ini?'"
        confirm-text="Hapus"
        cancel-text="Batal"
        :processing="deleteForm.processing"
        :danger="true"
        @update:open="confirmDelete = $event"
        @cancel="closeDeleteDialog"
        @confirm="destroyDocument"
      />
    </div>
  
    <MobileFab @click="createDialogOpen = true" />
    <Dialog :open="createDialogOpen" @close="createDialogOpen = false">
      <div class="p-5 max-h-[85vh] overflow-y-auto">
        <CardHeader>
          <CardTitle>Tambah Dokumen Baru</CardTitle>
        </CardHeader>
        <CardContent>
          <div class="grid gap-4 md:grid-cols-2">
            <div>
              <label class="text-sm font-medium">Judul</label>
              <Input v-model="createForm.title" placeholder="Judul dokumen" />
            </div>
            <div>
              <label class="text-sm font-medium">Kategori</label>
              <Select v-model="createForm.category">
                <option value="surat-masuk">Surat Masuk</option>
                <option value="surat-keluar">Surat Keluar</option>
                <option value="proposal">Proposal</option>
                <option value="lpj">LPJ Kegiatan</option>
                <option value="sk-pengurus">SK Pengurus</option>
              </Select>
            </div>
            <div>
              <label class="text-sm font-medium">Nomor Dokumen</label>
              <Input v-model="createForm.document_number" placeholder="Nomor dokumen" />
            </div>
            <div>
              <label class="text-sm font-medium">Tanggal</label>
              <Input type="date" v-model="createForm.document_date" />
            </div>
            <div class="md:col-span-2">
              <label class="text-sm font-medium">Deskripsi</label>
              <Textarea v-model="createForm.description" placeholder="Deskripsi dokumen" />
            </div>
            <div class="md:col-span-2">
              <label class="text-sm font-medium">File</label>
              <Input type="file" @change="e => createForm.file = e.target.files[0]" />
            </div>
          </div>
          <div class="mt-4">
            <Button @click="submitCreate">Simpan</Button>
          </div>
        </CardContent>
      </div>
    </Dialog>
  </AuthenticatedLayout>

</template>