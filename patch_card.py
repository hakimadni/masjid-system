import re

with open('/Users/user/Work/masjid-system/resources/js/Components/ui/card/Card.vue', 'r') as f:
    content = f.read()

# Replace border-slate-200 and shadow-sm with something richer
content = content.replace(
    "'rounded-xl border border-slate-200 bg-white text-slate-900 shadow-sm'",
    "'rounded-2xl border border-emerald-900/5 bg-white text-slate-900 shadow-xl shadow-emerald-900/5 ring-1 ring-slate-100/50'"
)

with open('/Users/user/Work/masjid-system/resources/js/Components/ui/card/Card.vue', 'w') as f:
    f.write(content)
