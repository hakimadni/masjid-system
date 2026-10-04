import re

def patch_file(filepath, add_import=False):
    with open(filepath, 'r') as f:
        content = f.read()

    # Replace the mim character span with the Logo component
    old_logo_html = '<span class="text-white font-bold text-xl">م</span>'
    new_logo_html = '<ApplicationLogo class="w-6 h-6 text-white" />'
    content = content.replace(old_logo_html, new_logo_html)
    
    if add_import and 'ApplicationLogo' not in content:
        # insert import ApplicationLogo from '@/Components/ApplicationLogo.vue';
        # after import { Head, Link } from ...
        content = re.sub(
            r"(import \{.*?\} from '@inertiajs/vue3';?)", 
            r"\1\nimport ApplicationLogo from '@/Components/ApplicationLogo.vue';", 
            content, 
            count=1
        )

    with open(filepath, 'w') as f:
        f.write(content)

patch_file('/Users/user/Work/masjid-system/resources/js/Pages/Welcome.vue', add_import=True)
patch_file('/Users/user/Work/masjid-system/resources/js/Pages/Public/Home.vue', add_import=True)

