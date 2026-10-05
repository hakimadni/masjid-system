import re

with open('/Users/user/Work/masjid-system/resources/js/Components/ui/dropdown/ActionMenu.vue', 'r') as f:
    content = f.read()

# Add a ref to the details element
content = content.replace('<details class="group relative">', '<details ref="detailsEl" class="group relative">')
if 'import { ref }' not in content:
    content = content.replace('<script setup>', '<script setup>\nimport { ref } from "vue"')

# Add logic to close
script_inject = """
const detailsEl = ref(null)

const pick = (key) => {
  emit("select", key)
  if (detailsEl.value) {
    detailsEl.value.removeAttribute('open')
  }
}
"""

# Replace the old pick
old_pick = """const pick = (key) => {
  emit("select", key)
}"""
content = content.replace(old_pick, script_inject)

with open('/Users/user/Work/masjid-system/resources/js/Components/ui/dropdown/ActionMenu.vue', 'w') as f:
    f.write(content)

