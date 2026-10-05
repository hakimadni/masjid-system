import os

files_to_fix = [
    '/Users/user/Work/masjid-system/resources/js/Pages/Admin/Finance/Index.vue',
    '/Users/user/Work/masjid-system/resources/js/Pages/Animals/Index.vue',
    '/Users/user/Work/masjid-system/resources/js/Pages/Distributions/Index.vue'
]

for file in files_to_fix:
    with open(file, 'r') as f:
        content = f.read()
    
    # Remove overflow-hidden from the mobile cards
    content = content.replace('overflow-hidden rounded-3xl', 'rounded-3xl')
    
    with open(file, 'w') as f:
        f.write(content)
