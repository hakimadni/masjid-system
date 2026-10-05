import re

with open('/Users/user/Work/masjid-system/resources/js/Pages/Admin/Dashboard.vue', 'r') as f:
    content = f.read()

# Fix the non-existent routes that crashed Ziggy
content = content.replace("route('finance.transactions.create', { type: 'income' })", "route('finance.index')")
content = content.replace("route('finance.transactions.create', { type: 'expense' })", "route('finance.index')")
content = content.replace("route('announcements.create')", "route('announcements.index')")
content = content.replace("route('prayer-schedules.index')", "route('schedules.index')")

with open('/Users/user/Work/masjid-system/resources/js/Pages/Admin/Dashboard.vue', 'w') as f:
    f.write(content)
