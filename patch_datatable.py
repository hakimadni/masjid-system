import re

with open('/Users/user/Work/masjid-system/resources/js/Components/ui/table/DataTable.vue', 'r') as f:
    content = f.read()

# Enhance the desktop table wrapper
content = content.replace(
    'class="hidden md:block overflow-hidden rounded-2xl border border-white/70 bg-white/95 shadow-sm shadow-slate-950/5 ring-1 ring-slate-200/70"',
    'class="hidden md:block overflow-hidden rounded-3xl border border-emerald-900/5 bg-white shadow-xl shadow-emerald-900/5 ring-1 ring-slate-100/50"'
)

with open('/Users/user/Work/masjid-system/resources/js/Components/ui/table/DataTable.vue', 'w') as f:
    f.write(content)
