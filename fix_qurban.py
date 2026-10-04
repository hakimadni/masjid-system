import re

with open('/Users/user/Work/masjid-system/resources/js/Pages/Admin/Qurban/Index.vue', 'r') as f:
    content = f.read()

# Extract cards
cards = re.findall(r'(<Card[^>]*>.*?<\/Card>)', content, re.DOTALL)
extracted_html = []

for c in cards:
    if ('Buat ' in c or 'Tambah ' in c or 'Catat ' in c) and ('Filter' not in c and '<DataTable' not in c):
        inner = c[c.find('>')+1 : c.rfind('</Card>')].strip()
        extracted_html.append(inner)
        content = content.replace(c, '')
        
combined = '<hr class="my-6 border-slate-200" />'.join(extracted_html)

content = content.replace('import PaymentBadge', 'import Dialog from "@/Components/ui/dialog/Dialog.vue"\nimport MobileFab from "@/Components/MobileFab.vue"\nimport PaymentBadge')
content = content.replace('const createForm =', 'const createDialogOpen = ref(false)\nconst createForm =')

# Replace submit
content = content.replace('const submitCreate = () => createForm.post(route("qurban.store"), { preserveScroll: true })', 'const submitCreate = () => createForm.post(route("qurban.store"), { preserveScroll: true, onSuccess: () => { createDialogOpen.value = false; createForm.reset() } })')


dialog_block = f"""
    <MobileFab @click="createDialogOpen = true" />
    <Dialog :open="createDialogOpen" @close="createDialogOpen = false">
      <div class="p-5 max-h-[85vh] overflow-y-auto">
        {combined}
      </div>
    </Dialog>
  </AuthenticatedLayout>
"""
content = content.replace('</AuthenticatedLayout>', dialog_block)

with open('/Users/user/Work/masjid-system/resources/js/Pages/Admin/Qurban/Index.vue', 'w') as f:
    f.write(content)
