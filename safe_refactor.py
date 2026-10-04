import os
import re
import subprocess

vue_files = []
for root, _, files in os.walk('/Users/user/Work/masjid-system/resources/js/Pages'):
    for file in files:
        if file.endswith('Index.vue'):
            vue_files.append(os.path.join(root, file))

for file in vue_files:
    if 'Distributions' in file or 'Finance/Index.vue' in file:
        continue
        
    with open(file, 'r') as f:
        original = f.read()
        
    content = original
    
    # Extract cards
    cards = re.findall(r'(<Card[^>]*>.*?<\/Card>)', content, re.DOTALL)
    extracted_html = []
    
    for c in cards:
        if ('Buat ' in c or 'Tambah ' in c or 'Catat ' in c) and ('Filter' not in c and '<DataTable' not in c):
            inner = c[c.find('>')+1 : c.rfind('</Card>')].strip()
            extracted_html.append(inner)
            content = content.replace(c, '')
            
    if extracted_html:
        combined = '<hr class="my-6 border-slate-200" />'.join(extracted_html)
        
        # Safe imports
        if 'import Dialog' not in content:
            content = content.replace('<script setup>', '<script setup>\nimport Dialog from "@/Components/ui/dialog/Dialog.vue"\nimport MobileFab from "@/Components/MobileFab.vue"')
            
        if 'const createDialogOpen' not in content:
            content = content.replace('const createForm =', 'const createDialogOpen = ref(false)\nconst createForm =')
            content = content.replace('const form =', 'const createDialogOpen = ref(false)\nconst form =')
            
        if 'import { ref' not in content and 'import { computed, ref' not in content:
            content = content.replace('<script setup>', '<script setup>\nimport { ref } from "vue"')

        # Append dialog
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
        
        # Write and test
        with open(file, 'w') as f:
            f.write(content)
            
        res = subprocess.run(['npm', 'run', 'build'], cwd='/Users/user/Work/masjid-system', capture_output=True)
        if res.returncode == 0:
            print(f"SUCCESS: {file}")
        else:
            print(f"FAILED: {file}, reverting.")
            with open(file, 'w') as f:
                f.write(original)

