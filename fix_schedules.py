import re

with open('/Users/user/Work/masjid-system/resources/js/Pages/Admin/Schedules/Index.vue', 'r') as f:
    content = f.read()

# Since Schedule has TWO distinct forms (Prayer form and Service form), we should either put them in tabs within a single Dialog, or create two separate Dialogs.
# A single dialog with two sections separated by an HR is fine too. Let's see how they were extracted:

cards = re.findall(r'(<Card[^>]*>.*?<\/Card>)', content, re.DOTALL)
extracted_html = []

for c in cards:
    # Look for the cards that are NOT the filter card and NOT the datatable card
    if ('Filter' not in c and '<DataTable' not in c and '<TableSkeleton' not in c):
        if 'ref="prayerSection"' in c or 'ref="serviceSection"' in c:
            inner = c[c.find('>')+1 : c.rfind('</Card>')].strip()
            extracted_html.append(inner)
            content = content.replace(c, '')

if extracted_html:
    combined = '<hr class="my-6 border-slate-200" />'.join(extracted_html)

    # Let's ensure MobileFab triggers the dialog. There's currently no createDialogOpen ref here if it missed the bulk update.
    if 'const createDialogOpen' not in content:
        content = content.replace('const prayerForm =', 'const createDialogOpen = ref(false)\nconst prayerForm =')
        
    if 'import Dialog' not in content:
        content = content.replace('import ConfirmDialog', 'import Dialog from "@/Components/ui/dialog/Dialog.vue"\nimport ConfirmDialog')
        if 'import MobileFab' not in content:
            content = content.replace('import Dialog', 'import Dialog from "@/Components/ui/dialog/Dialog.vue"\nimport MobileFab from "@/Components/MobileFab.vue"')

    # Inject onSuccess
    content = content.replace(
        'prayerForm.post(route("schedules.store-prayer"), { preserveScroll: true })',
        'prayerForm.post(route("schedules.store-prayer"), { preserveScroll: true, onSuccess: () => { createDialogOpen.value = false; prayerForm.reset(); } })'
    )
    content = content.replace(
        'serviceForm.post(route("schedules.store-service"), { preserveScroll: true })',
        'serviceForm.post(route("schedules.store-service"), { preserveScroll: true, onSuccess: () => { createDialogOpen.value = false; serviceForm.reset(); } })'
    )

    # Replace the existing dialog block if it exists (from the bulk refactor), or append if missing
    if '<Dialog :open="createDialogOpen"' in content:
        # It already exists, but might be empty or missing these forms
        old_dialog = re.search(r'<Dialog :open="createDialogOpen".*?</Dialog>', content, re.DOTALL)
        if old_dialog:
            new_dialog = f"""<Dialog :open="createDialogOpen" @close="createDialogOpen = false">
      <div class="p-5 max-h-[85vh] overflow-y-auto">
        {combined}
      </div>
    </Dialog>"""
            content = content.replace(old_dialog.group(0), new_dialog)
    else:
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

with open('/Users/user/Work/masjid-system/resources/js/Pages/Admin/Schedules/Index.vue', 'w') as f:
    f.write(content)
