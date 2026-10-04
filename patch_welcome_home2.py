import re

def fix_import(filepath):
    with open(filepath, 'r') as f:
        content = f.read()

    if 'ApplicationLogo' not in content:
        # insert import ApplicationLogo from '@/Components/ApplicationLogo.vue';
        # after import { Head, Link } from ...
        content = re.sub(
            r"(import \{.*?\} from ['\"]@inertiajs/vue3['\"];?)", 
            r"\1\nimport ApplicationLogo from '@/Components/ApplicationLogo.vue';", 
            content, 
            count=1
        )

    with open(filepath, 'w') as f:
        f.write(content)

fix_import('/Users/user/Work/masjid-system/resources/js/Pages/Welcome.vue')
fix_import('/Users/user/Work/masjid-system/resources/js/Pages/Public/Home.vue')
