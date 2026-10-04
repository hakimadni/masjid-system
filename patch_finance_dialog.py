import re

with open('/Users/user/Work/masjid-system/resources/js/Pages/Admin/Finance/Index.vue', 'r') as f:
    content = f.read()

# Make createDialogOpen ref available
if 'const createDialogOpen = ref(false)' not in content:
    content = content.replace('const rejectionDialog = ref({', 'const createDialogOpen = ref(false)\nconst rejectionDialog = ref({')

# Also handle submitCreate to close the dialog on success
content = content.replace(
    'const submitCreate = () => createForm.post(route("finance.store"), { preserveScroll: true })',
    'const submitCreate = () => createForm.post(route("finance.store"), { preserveScroll: true, onSuccess: () => { createDialogOpen.value = false; createForm.reset() } })'
)

# Update scrollToForm
content = content.replace(
    "const scrollToForm = () => {\n  document.getElementById('create-form')?.scrollIntoView({ behavior: 'smooth' })\n}",
    "const scrollToForm = () => {\n  createDialogOpen.value = true\n}"
)

# Replace the desktop button to also just say "Tambah Transaksi" and call scrollToForm (which now opens dialog)
# It's already doing @click="scrollToForm", so that's fine. Wait, let's remove the "hidden md:inline-flex" so it's always visible on top if users want, or we just rely on MobileFab. We'll leave it hidden on mobile to enforce FAB usage.

# Now extract the <Card id="create-form"> block
start_idx = content.find('<Card id="create-form"')
end_idx = content.find('</Card>', start_idx) + 7

form_block = content[start_idx:end_idx]

# Remove it from the original location
content = content[:start_idx] + content[end_idx:]

# Ensure Dialog is imported
if 'import Dialog' not in content:
    content = content.replace('import ConfirmDialog', 'import Dialog from "@/Components/ui/dialog/Dialog.vue"\nimport ConfirmDialog')

# Re-format the form block to fit nicely inside the Dialog
dialog_form = form_block.replace('<Card id="create-form" class="p-5">', '<div class="p-5 overflow-y-auto max-h-[85vh]">').replace('</Card>', '</div>')

dialog_html = f"""
    <MobileFab @click="createDialogOpen = true" />
    <Dialog :open="createDialogOpen" @close="createDialogOpen = false">
      {dialog_form}
    </Dialog>
  </AuthenticatedLayout>
"""

content = content.replace('</AuthenticatedLayout>', dialog_html)

with open('/Users/user/Work/masjid-system/resources/js/Pages/Admin/Finance/Index.vue', 'w') as f:
    f.write(content)

