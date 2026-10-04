import re

with open('/Users/user/Work/masjid-system/resources/js/Layouts/AuthenticatedLayout.vue', 'r') as f:
    content = f.read()

# Remove Building2 import if it's there
content = content.replace("import { Building2, ChevronRight, ChevronDown, Menu, Search } from \"lucide-vue-next\"", 
                          "import { ChevronRight, ChevronDown, Menu, Search } from \"lucide-vue-next\"")

# Add ApplicationLogo import if not there
if 'ApplicationLogo' not in content:
    content = re.sub(
        r"(import UserMenu from \"@/Components/ui/menu/UserMenu\.vue\")",
        r"\1\nimport ApplicationLogo from \"@/Components/ApplicationLogo.vue\"",
        content,
        count=1
    )

# Replace <Building2 ... /> with <ApplicationLogo ... />
content = content.replace('<Building2 class="h-6 w-6" />', '<ApplicationLogo class="h-8 w-8" />')

with open('/Users/user/Work/masjid-system/resources/js/Layouts/AuthenticatedLayout.vue', 'w') as f:
    f.write(content)
