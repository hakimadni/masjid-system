import os
import re

directories = [
    '/Users/user/Work/masjid-system/resources/js/Pages'
]

def find_vue_files(dirs):
    vue_files = []
    for d in dirs:
        for root, _, files in os.walk(d):
            for file in files:
                if file == 'Index.vue':
                    vue_files.append(os.path.join(root, file))
    return vue_files

vue_files = find_vue_files(directories)

for file in vue_files:
    with open(file, 'r') as f:
        content = f.read()

    # Heuristic to find the "Create" card
    # It usually has text like "Baru", "Tambah", "Buat", and contains inputs.
    # A safe way is to find a <Card> that is NOT the filter card (doesn't have "Filter") 
    # and NOT the table card (doesn't have <DataTable).
    
    # Let's extract all <Card>...</Card> blocks
    # Actually, because of nested tags, regex for HTML blocks is hard.
    # We can split by "<Card"
    
    # We will look for something simpler: 
    # Is there a <p class="text-sm font-semibold text-slate-900">Buat ... Baru</p> or Tambah ...
    
    create_title_match = re.search(r'<Card[^>]*>.*?<p[^>]*>(Buat|Tambah|Catat)\s+(.*?(Baru|Data|Transaksi).*?)</p>', content, re.DOTALL | re.IGNORECASE)
    
    if not create_title_match:
        # Check another pattern like <p class="text-sm font-medium text-slate-700">Tambah Data Distribusi</p>
        create_title_match = re.search(r'<Card[^>]*>.*?<p[^>]*>(Tambah\s+Data\s+.*?)</p>', content, re.DOTALL | re.IGNORECASE)
        
    if create_title_match:
        # We found a Create Card!
        # Let's try to extract the whole card manually
        start_idx = content.rfind('<Card', 0, create_title_match.start())
        end_idx = content.find('</Card>', start_idx) + 7
        
        if start_idx != -1 and end_idx != -1:
            card_html = content[start_idx:end_idx]
            
            # Verify it doesn't contain <DataTable
            if '<DataTable' not in card_html and 'Filter' not in card_html:
                print(f"Refactoring {file}...")
                
                # We can extract the inner content of the card
                inner_html = card_html[card_html.find('>')+1 : card_html.rfind('</Card>')].strip()
                
                # Replace the card in the content with nothing
                content = content[:start_idx] + content[end_idx:]
                
                # Make sure Dialog and MobileFab are imported
                if 'import Dialog' not in content:
                    content = content.replace('import Card', 'import Card from "@/Components/ui/card/Card.vue"\nimport Dialog from "@/Components/ui/dialog/Dialog.vue"\nimport MobileFab from "@/Components/MobileFab.vue"')
                
                if 'createDialogOpen' not in content:
                    # Inject ref
                    script_end = content.find('</script>')
                    if 'import { ref' not in content and 'import { computed, ref' not in content:
                         # We might need to import ref, but usually it's there. 
                         pass
                    
                    # inject const createDialogOpen = ref(false)
                    content = content.replace('const createForm = useForm', 'const createDialogOpen = ref(false)\nconst createForm = useForm')
                    content = content.replace('const form = useForm', 'const createDialogOpen = ref(false)\nconst form = useForm')
                
                # Find the submit function and add dialog close logic
                # Either submitCreate or submit
                submit_match = re.search(r'const (submitCreate|submit) = \(\) => (createForm|form)\.post\([^)]+\)', content)
                if submit_match:
                    func_name = submit_match.group(1)
                    form_name = submit_match.group(2)
                    # We will replace it with a function that closes the dialog on success
                    # Wait, simpler to just inject `onSuccess: () => { createDialogOpen.value = false; }`
                    new_submit = f"const {func_name} = () => {form_name}.post(route({form_name}.post_url || window.location.href), {{ preserveScroll: true, onSuccess: () => {{ createDialogOpen.value = false; {form_name}.reset() }} }})".replace("route(createForm.post_url || window.location.href)", "window.location.href") # this is risky, we shouldn't mess with the route.
                
                # Safer: just rely on the existing post call, use regex to inject onSuccess
                content = re.sub(
                    r'(const (?:submitCreate|submit) = \(\) => (?:createForm|form)\.post\([^,]+)(?:,\s*\{[^}]+\})?\)',
                    r'\1, { preserveScroll: true, onSuccess: () => { createDialogOpen.value = false; \2.reset() } })',
                    content
                )
                
                # Now append Dialog at the end of AuthenticatedLayout
                dialog_block = f"""
    <MobileFab @click="createDialogOpen = true" />
    <Dialog :open="createDialogOpen" @close="createDialogOpen = false">
      <div class="p-5 max-h-[85vh] overflow-y-auto">
        {inner_html}
      </div>
    </Dialog>
  </AuthenticatedLayout>
"""
                content = content.replace('</AuthenticatedLayout>', dialog_block)
                
                with open(file, 'w') as f:
                    f.write(content)
                print(f"  -> Done.")

