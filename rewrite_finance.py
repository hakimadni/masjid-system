import re

with open('/Users/user/Work/masjid-system/resources/js/Pages/Admin/Finance/Index.vue', 'r') as f:
    content = f.read()

# 1. ADD detailDialog ref
if 'const detailDialog = ref' not in content:
    content = content.replace('const createDialogOpen = ref(false)', 'const createDialogOpen = ref(false)\nconst detailDialog = ref({ open: false, item: null })\n\nconst openDetailDialog = (item) => {\n  detailDialog.value.item = item\n  detailDialog.value.open = true\n}\nconst closeDetailDialog = () => {\n  detailDialog.value.open = false\n  setTimeout(() => { detailDialog.value.item = null }, 300)\n}')

# 2. MODIFY mobile card to remove ActionMenu and make it clickable
old_card = """<div class="group relative flex flex-col justify-between rounded-3xl border border-emerald-900/5 bg-white shadow-lg shadow-emerald-900/5 ring-1 ring-slate-100/50 mb-3 p-4 transition-all active:scale-95">"""
new_card = """<div @click="openDetailDialog(item)" class="cursor-pointer group relative flex flex-col justify-between rounded-3xl border border-emerald-900/5 bg-white shadow-lg shadow-emerald-900/5 ring-1 ring-slate-100/50 mb-3 p-4 transition-all active:scale-95">"""
content = content.replace(old_card, new_card)

# Remove action menu from mobile card
old_mobile_action = """<div class="flex justify-end mt-1 items-center gap-1">
                          <StatusBadge size="sm" :status="item.status" />
                          <ActionMenu :items="actionItems(item)" @select="handleAction(item, $event)" />
                        </div>"""
new_mobile_action = """<div class="flex justify-end mt-1 items-center gap-1">
                          <StatusBadge size="sm" :status="item.status" />
                        </div>"""
content = content.replace(old_mobile_action, new_mobile_action)

# 3. MODIFY desktop table to make rows clickable and remove action menu
old_tr = """<tr v-for="item in financeEntries.data" :key="item.id" class="border-t border-slate-100">"""
new_tr = """<tr v-for="item in financeEntries.data" :key="item.id" @click="openDetailDialog(item)" class="border-t border-slate-100 cursor-pointer hover:bg-slate-50 transition-colors">"""
content = content.replace(old_tr, new_tr)

old_desktop_action = """<td class="px-4 py-3 text-right">
                    <div class="flex justify-end">
                      <ActionMenu :items="actionItems(item)" @select="handleAction(item, $event)" />
                    </div>
                  </td>"""
new_desktop_action = """<td class="px-4 py-3 text-right">
                    <div class="flex justify-end text-slate-400">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </div>
                  </td>"""
content = content.replace(old_desktop_action, new_desktop_action)

# Update handleAction to close detail dialog
old_handle_action = """const handleAction = (item, key) => {
  if (key === "approve") updateStatus(item.id, "approved")
  if (key === "reject") openRejectDialog(item.id)
}"""
new_handle_action = """const handleAction = (item, key) => {
  if (key === "approve") {
    updateStatus(item.id, "approved")
    closeDetailDialog()
  }
  if (key === "reject") {
    openRejectDialog(item.id)
    closeDetailDialog()
  }
}"""
content = content.replace(old_handle_action, new_handle_action)

# Add Detail Dialog HTML at the end before closing template
detail_dialog_html = """
    <Dialog :open="detailDialog.open" @update:open="detailDialog.open = $event" @close="closeDetailDialog">
      <div class="p-5 overflow-y-auto max-h-[85vh]" v-if="detailDialog.item">
        <p class="text-sm font-semibold text-slate-900 mb-4">Detail Transaksi</p>
        
        <div class="flex gap-3 mb-6" v-if="detailDialog.item.status === 'pending'">
            <Button variant="success" class="flex-1 shadow-md" @click="handleAction(detailDialog.item, 'approve')">Setujui (Approve)</Button>
            <button class="flex-1 rounded-xl bg-rose-500 hover:bg-rose-600 text-white font-medium text-sm shadow-md shadow-rose-950/10" @click="handleAction(detailDialog.item, 'reject')">Tolak (Reject)</button>
        </div>
        <div class="flex gap-3 mb-6" v-else>
            <div class="flex-1 text-center bg-slate-50 p-3 rounded-xl border border-slate-100 flex flex-col items-center justify-center">
               <p class="text-xs text-slate-500 mb-2">Status Saat Ini</p>
               <StatusBadge :status="detailDialog.item.status" />
            </div>
        </div>

        <h3 class="text-lg font-bold text-slate-900 mb-4">{{ detailDialog.item.title }}</h3>
        <div class="space-y-3 text-sm">
            <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                <span class="text-slate-500">Nominal</span>
                <span class="font-bold text-slate-900 text-base"><MoneyDisplay :value="detailDialog.item.amount" /></span>
            </div>
            <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                <span class="text-slate-500">Tipe</span>
                <span class="font-medium" :class="detailDialog.item.entry_type === 'income' ? 'text-emerald-600' : 'text-rose-600'">
                  {{ detailDialog.item.entry_type === 'income' ? 'Uang Masuk' : 'Uang Keluar' }}
                </span>
            </div>
            <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                <span class="text-slate-500">Kategori</span>
                <span class="font-medium text-slate-900">{{ detailDialog.item.category }}</span>
            </div>
            <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                <span class="text-slate-500">Tanggal</span>
                <span class="font-medium text-slate-900">{{ detailDialog.item.transaction_date }}</span>
            </div>
            <div v-if="detailDialog.item.notes" class="pt-2">
                <span class="block text-slate-500 mb-1 text-xs">Catatan / Keterangan</span>
                <p class="text-slate-900 bg-slate-50 p-3 rounded-xl leading-relaxed">{{ detailDialog.item.notes }}</p>
            </div>
        </div>
      </div>
    </Dialog>
"""

content = content.replace('<MobileFab @click="scrollToForm" />', detail_dialog_html + '\n    <MobileFab @click="scrollToForm" />')

with open('/Users/user/Work/masjid-system/resources/js/Pages/Admin/Finance/Index.vue', 'w') as f:
    f.write(content)
