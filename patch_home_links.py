import re

with open('/Users/user/Work/masjid-system/resources/js/Pages/Public/Home.vue', 'r') as f:
    content = f.read()

# Fix the navigation logo/link back to landing page
content = content.replace('<Link href="/" class="text-sm font-semibold text-white transition-colors">Beranda</Link>',
                          '<Link href="/portal" class="text-sm font-semibold text-white transition-colors">Beranda</Link>')
content = content.replace('<Link href="/"', '<Link href="/portal"')

with open('/Users/user/Work/masjid-system/resources/js/Pages/Public/Home.vue', 'w') as f:
    f.write(content)
