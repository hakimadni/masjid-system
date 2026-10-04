import os
import re

vue_files = []
for root, _, files in os.walk('/Users/user/Work/masjid-system/resources/js/Pages'):
    for file in files:
        if file.endswith('Index.vue'):
            vue_files.append(os.path.join(root, file))

for file in vue_files:
    # Skip Distributions since I already manually added a Dialog there
    if 'Distributions/Index.vue' in file:
        continue
    # Skip Finance since I already did it
    if 'Finance/Index.vue' in file:
        continue

    with open(file, 'r') as f:
        content = f.read()
    
    cards = re.findall(r'(<Card[^>]*>.*?<\/Card>)', content, re.DOTALL)
    
    extracted_html = []
    
    for c in cards:
        if ('Buat ' in c or 'Tambah ' in c or 'Catat ' in c) and ('Filter' not in c and '<DataTable' not in c):
            # Extract inner HTML of the card
            inner_html = c[c.find('>')+1 : c.rfind('</Card>')].strip()
            extracted_html.append(inner_html)
            
            # Remove the card from content
            content = content.replace(c, '')
            
    if extracted_html:
        print(f"Refactoring {file} ({len(extracted_html)} forms found)")
        
        # Combine all extracted forms
        combined_forms = '<hr class="my-6 border-slate-200" />'.join(extracted_html)
        
        # Import components
        if 'import Dialog' not in content:
            # We need to inject import correctly
            if 'import Card' in content:
                content = content.replace('import Card', 'import Dialog from "@/Components/ui/dialog/Dialog.vue"\nimport MobileFab from "@/Components/MobileFab.vue"\nimport Card')
            else:
                content = content.replace('import { Head', 'import Dialog from "@/Components/ui/dialog/Dialog.vue"\nimport MobileFab from "@/Components/MobileFab.vue"\nimport { Head')

        # Add ref for Dialog state
        if 'const createDialogOpen' not in content:
            if 'const createForm =' in content:
                content = content.replace('const createForm =', 'const createDialogOpen = ref(false)\nconst createForm =')
            elif 'const form =' in content:
                content = content.replace('const form =', 'const createDialogOpen = ref(false)\nconst form =')
            
            # Ensure 'ref' is imported from vue
            if 'import { ref }' not in content and 'import { computed, ref }' not in content and 'import { ref,' not in content:
                if 'import { computed }' in content:
                    content = content.replace('import { computed }', 'import { computed, ref }')
                else:
                    content = content.replace('<script setup>', '<script setup>\nimport { ref } from "vue"')

        # Inject onSuccess into submit functions to close dialog
        # Find something like: createForm.post(route('events.store'), { preserveScroll: true })
        # We'll just do a global replace for common submit lines
        content = re.sub(
            r'((?:createForm|form)\.post\([^,]+)(,\s*\{\s*preserveScroll:\s*true\s*\})?(\))',
            r'\1, { preserveScroll: true, onSuccess: () => { createDialogOpen.value = false; if(typeof createForm !== "undefined") createForm.reset(); else if(typeof form !== "undefined") form.reset(); } }\3',
            content
        )
        # Catch versions without options parameter at all
        content = re.sub(
            r'((?:createForm|form)\.post\([^,)]+\))(\s*)$',
            r'\1.replace(")", ", { preserveScroll: true, onSuccess: () => { createDialogOpen.value = false; } })")\2', # wait, regex replace of string is bad. 
            content, flags=re.MULTILINE
        )
        # Safer regex for injecting options into empty .post(route(...))
        content = re.sub(
            r'((?:createForm|form)\.post\([^,)]+)\)',
            r'\1, { preserveScroll: true, onSuccess: () => { createDialogOpen.value = false; } })',
            content
        )

        # Append FAB and Dialog to the end of AuthenticatedLayout
        dialog_block = f"""
    <MobileFab @click="createDialogOpen = true" />
    <Dialog :open="createDialogOpen" @close="createDialogOpen = false">
      <div class="p-5 max-h-[85vh] overflow-y-auto">
        {combined_forms}
      </div>
    </Dialog>
  </AuthenticatedLayout>
"""
        content = content.replace('</AuthenticatedLayout>', dialog_block)
        
        with open(file, 'w') as f:
            f.write(content)

