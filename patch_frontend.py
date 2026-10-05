import re

with open('/Users/user/Work/masjid-system/resources/js/Pages/Admin/Finance/Index.vue', 'r') as f:
    content = f.read()

# Update createForm
old_createForm = """const createForm = useForm({
  title: "",
  entry_type: "income",
  finance_account_id: props.accounts[0]?.id ?? "",
  finance_category_id: "",
  amount: "",
  payment_method: "cash",
  status: "pending",
  transaction_date: new Date().toISOString().slice(0, 10),
  notes: "",
})"""
new_createForm = """const createForm = useForm({
  title: "",
  entry_type: "income",
  finance_account_id: props.accounts[0]?.id ?? "",
  finance_category_id: "",
  amount: "",
  payment_method: "cash",
  status: "pending",
  transaction_date: new Date().toISOString().slice(0, 10),
  notes: "",
  attachment: null,
})"""
content = content.replace(old_createForm, new_createForm)

# Update submit method for forceFormData because of file upload
# Inertia auto handles form data if a file is present, but just in case
old_submit = """const submitCreate = () => createForm.post(route("finance.store"), { preserveScroll: true, onSuccess: () => { createDialogOpen.value = false; createForm.reset() } })"""
new_submit = """const submitCreate = () => createForm.post(route("finance.store"), { preserveScroll: true, forceFormData: true, onSuccess: () => { createDialogOpen.value = false; createForm.reset() } })"""
content = content.replace(old_submit, new_submit)

# Add file input to the form
old_form_inputs = """<div class="xl:col-span-2">
            <label class="mb-1.5 block text-sm font-medium text-slate-900">Catatan</label>
            <Textarea v-model="createForm.notes" placeholder="Opsional, keterangan tambahan..." />
          </div>"""
new_form_inputs = """<div class="xl:col-span-2">
            <label class="mb-1.5 block text-sm font-medium text-slate-900">Catatan</label>
            <Textarea v-model="createForm.notes" placeholder="Opsional, keterangan tambahan..." />
          </div>
          <div class="xl:col-span-2">
            <label class="mb-1.5 block text-sm font-medium text-slate-900">Bukti Pembayaran / Nota (Opsional)</label>
            <input type="file" @change="e => createForm.attachment = e.target.files[0]" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100" />
            <p v-if="errorFor('attachment')" class="mt-1 text-xs text-rose-500">{{ errorFor('attachment') }}</p>
          </div>"""
content = content.replace(old_form_inputs, new_form_inputs)

# Add image display to the detail modal
old_detail = """<div v-if="detailDialog.item.notes" class="pt-2">
                <span class="block text-slate-500 mb-1 text-xs">Catatan / Keterangan</span>
                <p class="text-slate-900 bg-slate-50 p-3 rounded-xl leading-relaxed">{{ detailDialog.item.notes }}</p>
            </div>
        </div>"""
new_detail = """<div v-if="detailDialog.item.notes" class="pt-2">
                <span class="block text-slate-500 mb-1 text-xs">Catatan / Keterangan</span>
                <p class="text-slate-900 bg-slate-50 p-3 rounded-xl leading-relaxed">{{ detailDialog.item.notes }}</p>
            </div>
            <div v-if="detailDialog.item.attachment_url" class="pt-2">
                <span class="block text-slate-500 mb-2 text-xs">Bukti Lampiran / Nota</span>
                <a :href="detailDialog.item.attachment_url" target="_blank">
                  <img :src="detailDialog.item.attachment_url" class="w-full h-auto rounded-xl border border-slate-200 shadow-sm" alt="Bukti Transaksi" />
                </a>
            </div>
        </div>"""
content = content.replace(old_detail, new_detail)

with open('/Users/user/Work/masjid-system/resources/js/Pages/Admin/Finance/Index.vue', 'w') as f:
    f.write(content)
