import re

with open('/Users/user/Work/masjid-system/resources/js/Layouts/AuthenticatedLayout.vue', 'r') as f:
    content = f.read()

if 'GlobalToast' not in content:
    content = content.replace('import MobileBottomNav from "@/Components/MobileBottomNav.vue"', 'import MobileBottomNav from "@/Components/MobileBottomNav.vue"\nimport GlobalToast from "@/Components/ui/toast/GlobalToast.vue"')
    
    # Inject it right at the top of the <template> block, or just inside the main wrapper
    content = content.replace('<template>\n  <div>', '<template>\n  <div>\n    <GlobalToast />')
    # Or if it starts with <div class="flex...
    content = content.replace('<template>\n  <div class="flex', '<template>\n  <GlobalToast />\n  <div class="flex')
    
with open('/Users/user/Work/masjid-system/resources/js/Layouts/AuthenticatedLayout.vue', 'w') as f:
    f.write(content)
