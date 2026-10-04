with open('/Users/user/Work/masjid-system/resources/js/Layouts/AuthenticatedLayout.vue', 'r') as f:
    content = f.read()

content = content.replace(r'\"@/Components/ApplicationLogo.vue\"', '"@/Components/ApplicationLogo.vue"')

with open('/Users/user/Work/masjid-system/resources/js/Layouts/AuthenticatedLayout.vue', 'w') as f:
    f.write(content)
