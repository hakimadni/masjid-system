import re

def add_import_if_missing(filepath):
    with open(filepath, 'r') as f:
        content = f.read()

    if 'import ApplicationLogo' not in content:
        content = re.sub(
            r"(import \{.*?\} from ['\"]@inertiajs/vue3['\"];?)", 
            r"\1\nimport ApplicationLogo from '@/Components/ApplicationLogo.vue';", 
            content, 
            count=1
        )

    with open(filepath, 'w') as f:
        f.write(content)

add_import_if_missing('/Users/user/Work/masjid-system/resources/js/Pages/Welcome.vue')
add_import_if_missing('/Users/user/Work/masjid-system/resources/js/Pages/Public/Home.vue')
