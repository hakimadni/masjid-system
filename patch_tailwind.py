import re

with open('/Users/user/Work/masjid-system/tailwind.config.js', 'r') as f:
    content = f.read()

content = content.replace("'Figtree', ...defaultTheme.fontFamily.sans", "'Plus Jakarta Sans', ...defaultTheme.fontFamily.sans")

with open('/Users/user/Work/masjid-system/tailwind.config.js', 'w') as f:
    f.write(content)

with open('/Users/user/Work/masjid-system/resources/views/app.blade.php', 'r') as f:
    app_blade = f.read()

app_blade = app_blade.replace('family=figtree:400,500,600&display=swap', 'family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap')

with open('/Users/user/Work/masjid-system/resources/views/app.blade.php', 'w') as f:
    f.write(app_blade)
