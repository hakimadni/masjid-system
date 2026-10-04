import os
import re

vue_files = []
for root, _, files in os.walk('/Users/user/Work/masjid-system/resources/js/Pages'):
    for file in files:
        if file.endswith('Index.vue'):
            vue_files.append(os.path.join(root, file))

for file in vue_files:
    with open(file, 'r') as f:
        content = f.read()
    
    # Just print any file that has <Card and 'Buat' or 'Tambah'
    cards = re.findall(r'<Card.*?<\/Card>', content, re.DOTALL)
    for i, c in enumerate(cards):
        if 'Buat' in c or 'Tambah' in c or 'Catat' in c:
            if 'Filter' not in c and '<DataTable' not in c:
                print(f"Found candidate in {file} (Card {i+1})")
