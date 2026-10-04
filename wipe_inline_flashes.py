import os
import re

for root, _, files in os.walk('/Users/user/Work/masjid-system/resources/js/Pages'):
    for file in files:
        if file.endswith('.vue'):
            path = os.path.join(root, file)
            with open(path, 'r') as f:
                content = f.read()
            
            # Look for typical inline flash blocks
            # e.g. <div v-if="flash.success" class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">\n        {{ flash.success }}\n      </div>
            new_content = re.sub(
                r'<div[^>]*v-if="flash\.success"[^>]*>.*?<\/div>',
                '',
                content,
                flags=re.DOTALL
            )
            
            # Also catch <ToastMessage v-if="flash.success" :message="flash.success" />
            new_content = re.sub(
                r'<ToastMessage[^>]*v-if="flash\.success"[^>]*\/>',
                '',
                new_content,
                flags=re.DOTALL
            )

            if content != new_content:
                with open(path, 'w') as f:
                    f.write(new_content)
