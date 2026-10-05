import re

with open('/Users/user/Work/masjid-system/app/Http/Requests/AdminScheduleStoreRequest.php', 'r') as f:
    content = f.read()

# Add recurrence to prayer rules
content = content.replace(
    "'notes' => ['nullable', 'string', 'max:1000'],",
    "'notes' => ['nullable', 'string', 'max:1000'],\n                'recurrence' => ['nullable', 'in:none,daily,weekly,monthly'],"
)

# Add recurrence to service rules
content = content.replace(
    "'notes' => ['nullable', 'string', 'max:1000'],",
    "'notes' => ['nullable', 'string', 'max:1000'],\n            'recurrence' => ['nullable', 'in:none,daily,weekly,monthly'],"
)

with open('/Users/user/Work/masjid-system/app/Http/Requests/AdminScheduleStoreRequest.php', 'w') as f:
    f.write(content)

