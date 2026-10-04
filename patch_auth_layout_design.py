import re

with open('/Users/user/Work/masjid-system/resources/js/Layouts/AuthenticatedLayout.vue', 'r') as f:
    content = f.read()

content = content.replace(
    'class="rounded-[1.75rem] border border-white/70 bg-white/80 shadow-lg shadow-slate-950/5 backdrop-blur"',
    'class="rounded-[2rem] border border-emerald-900/5 bg-white/80 shadow-xl shadow-emerald-900/5 backdrop-blur-xl ring-1 ring-white"'
)

content = content.replace(
    'class="flex h-full flex-col overflow-y-auto rounded-[2.5rem] border border-white/60 bg-white/60 shadow-xl shadow-slate-950/5 backdrop-blur-xl"',
    'class="flex h-full flex-col overflow-y-auto rounded-[2.5rem] border border-emerald-900/5 bg-white/70 shadow-2xl shadow-emerald-900/5 backdrop-blur-2xl ring-1 ring-white"'
)

# Enhance the workspace card
content = content.replace(
    'class="mt-5 rounded-2xl border border-emerald-100 bg-emerald-50/80 px-4 py-3"',
    'class="mt-5 rounded-2xl border border-emerald-200/50 bg-gradient-to-br from-emerald-50 to-teal-50/50 px-4 py-3 shadow-inner"'
)

with open('/Users/user/Work/masjid-system/resources/js/Layouts/AuthenticatedLayout.vue', 'w') as f:
    f.write(content)
