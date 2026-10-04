import re

with open('/Users/user/Work/masjid-system/resources/js/Pages/Distributions/Index.vue', 'r') as f:
    content = f.read()

# Add MobileFab import if not present
if "MobileFab" not in content:
    content = content.replace('import Button from "@/Components/ui/button/Button.vue"', 'import Button from "@/Components/ui/button/Button.vue"\nimport MobileFab from "@/Components/MobileFab.vue"')

# Convert the desktop inline form to be hidden on mobile
content = content.replace('<Card class="p-5">', '<Card class="hidden md:block p-5">')

# Add the #mobile-card slot to DataTable
mobile_card_slot = """<template #mobile-card="{ item }">
          <div class="group relative flex flex-col justify-between overflow-hidden rounded-3xl border border-emerald-900/5 bg-white shadow-lg shadow-emerald-900/5 ring-1 ring-slate-100/50 mb-3 p-4 transition-all active:scale-95">
            <div class="flex items-center justify-between">
              <div>
                <p class="font-bold text-slate-800 text-base leading-tight truncate">{{ item.recipient_name }}</p>
                <p class="text-xs text-slate-500 font-medium mt-1 truncate">{{ item.recipient_type }} • {{ item.package_count }} Paket</p>
              </div>
              <div class="text-right">
                <StatusBadge size="sm" :status="item.status" />
                <ActionMenu class="mt-1" :items="[{ key: 'delete', label: 'Hapus', tone: 'danger' }]" @select="deleteItem(item.id)" />
              </div>
            </div>
            <div v-if="item.status === 'pending'" class="mt-3">
               <Button variant="primary" class="w-full justify-center bg-emerald-600 hover:bg-emerald-700" @click="markDelivered(item.id)">
                  Tandai Selesai (Disalurkan)
               </Button>
            </div>
          </div>
        </template>
        <thead"""
content = content.replace('<thead', mobile_card_slot)

# Pass data to DataTable explicitly if it's missing (Wait, let's see how DataTable handles data here)
# If it uses <tr v-for>, we need to pass :data="distributions.data"
content = content.replace('<DataTable>', '<DataTable v-if="distributions.data.length" :data="distributions.data">')

# Add MobileFab at the end of AuthenticatedLayout
mobile_fab = """
    <!-- Mobile Floating Form Dialog (replaces inline form on mobile) -->
    <MobileFab @click="mobileFormOpen = true" />
    
    <Dialog :open="mobileFormOpen" @close="mobileFormOpen = false">
      <div class="p-5">
        <h3 class="text-lg font-bold text-slate-900 mb-4">Bagikan Daging</h3>
        <div class="space-y-4">
          <Select v-model="form.slaughtering_id" class="w-full">
            <option value="">— Pilih Penyembelihan —</option>
            <option v-for="s in slaughterings" :key="s.id" :value="s.id">{{ slaughteringLabel(s) }}</option>
          </Select>
          <Input v-model="form.recipient_name" placeholder="Nama Penerima" class="w-full h-12" />
          <Select v-model="form.recipient_type" class="w-full h-12">
            <option value="mustahik">Mustahik</option>
            <option value="penerima">Penerima Umum</option>
          </Select>
          <Input v-model="form.package_count" type="number" min="1" placeholder="Jumlah Paket" class="w-full h-12" />
          <Button @click="submitMobile" class="w-full h-12 bg-emerald-600">Simpan & Bagikan</Button>
        </div>
      </div>
    </Dialog>
  </AuthenticatedLayout>"""

content = content.replace('</AuthenticatedLayout>', mobile_fab)

# Add ref for mobileFormOpen and submitMobile method
script_inject = """
import Dialog from "@/Components/ui/dialog/Dialog.vue"
const mobileFormOpen = ref(false)
const submitMobile = () => {
    submit()
    mobileFormOpen.value = false
}
"""
content = content.replace('const form = useForm({', script_inject + '\nconst form = useForm({')

with open('/Users/user/Work/masjid-system/resources/js/Pages/Distributions/Index.vue', 'w') as f:
    f.write(content)

