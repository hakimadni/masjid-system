import re

with open('/Users/user/Work/masjid-system/resources/js/Pages/Admin/Schedules/Index.vue', 'r') as f:
    content = f.read()

content = content.replace('import MobileFab from "@/Components/MobileFab.vue" from "@/Components/ui/dialog/Dialog.vue"', 'import MobileFab from "@/Components/MobileFab.vue"')

with open('/Users/user/Work/masjid-system/resources/js/Pages/Admin/Schedules/Index.vue', 'w') as f:
    f.write(content)
