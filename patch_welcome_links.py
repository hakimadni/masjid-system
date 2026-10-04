import re

with open('/Users/user/Work/masjid-system/resources/js/Pages/Welcome.vue', 'r') as f:
    content = f.read()

# Fix the CTA links in Welcome.vue to point to /portal instead of /
content = content.replace('href="/"', 'href="/portal"')

with open('/Users/user/Work/masjid-system/resources/js/Pages/Welcome.vue', 'w') as f:
    f.write(content)
