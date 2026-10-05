import re

with open('/Users/user/Work/masjid-system/resources/js/Components/ui/dropdown/ActionMenu.vue', 'r') as f:
    content = f.read()

old_summary = """<summary class="list-none">
      <Button variant="ghost" size="icon" class="h-9 w-9">
        <MoreHorizontal class="h-4 w-4" />
      </Button>
    </summary>"""

new_summary = """<summary class="list-none cursor-pointer p-2 hover:bg-slate-100 rounded-full transition-colors flex items-center justify-center h-10 w-10">
      <MoreHorizontal class="h-5 w-5 text-slate-500" />
    </summary>"""

content = content.replace(old_summary, new_summary)
content = content.replace('import Button from "@/Components/ui/button/Button.vue"', '')

with open('/Users/user/Work/masjid-system/resources/js/Components/ui/dropdown/ActionMenu.vue', 'w') as f:
    f.write(content)
