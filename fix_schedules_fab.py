import re

with open('/Users/user/Work/masjid-system/resources/js/Pages/Admin/Schedules/Index.vue', 'r') as f:
    content = f.read()

# Add ref for activeFormType
if "const activeFormType = ref('prayer')" not in content:
    content = content.replace("const prayerForm =", "const activeFormType = ref('prayer')\nconst prayerForm =")

# Add function to open form
if "const openForm =" not in content:
    content = content.replace("const activeFormType = ref('prayer')", "const activeFormType = ref('prayer')\nconst openForm = (type) => { activeFormType.value = type; createDialogOpen.value = true }\n")

# Import SpeedDialFab
if 'import SpeedDialFab' not in content:
    content = content.replace('import MobileFab from "@/Components/MobileFab.vue"', 'import MobileFab from "@/Components/MobileFab.vue"\nimport SpeedDialFab from "@/Components/ui/button/SpeedDialFab.vue"')

# Add recurrence fields to forms
content = content.replace(
    'prayer_name: "subuh",',
    'prayer_name: "subuh",\n  recurrence: "none",'
)
content = content.replace(
    'role_type: "petugas",',
    'role_type: "petugas",\n  recurrence: "none",'
)

# Replace the MobileFab with SpeedDialFab
content = content.replace(
    '<MobileFab @click="createDialogOpen = true" />',
    """<SpeedDialFab 
      :options="[
        {key: 'prayer', label: 'Tambah Shalat/Khatib'}, 
        {key: 'service', label: 'Tambah Jadwal Petugas'}
      ]" 
      @select="openForm" 
    />"""
)

# Add conditional rendering to the Dialog sections
content = content.replace('<div class="mt-4 grid gap-3 md:grid-cols-2">', '<div class="mt-4 grid gap-3 md:grid-cols-2 lg:grid-cols-2">') # just normalizer

# Make sections conditionally visible
content = content.replace('ref="prayerSection"', 'ref="prayerSection" v-show="activeFormType === \'prayer\'"')
content = content.replace('ref="serviceSection"', 'ref="serviceSection" v-show="activeFormType === \'service\'"')

# Add Recurrence dropdown to Prayer form (right before the Submit button div)
prayer_submit_div = """<div class="flex items-end">
              <Button :disabled="prayerForm.processing" @click="submitPrayer">{{ prayerForm.processing ? "Menyimpan..." : "Simpan Jadwal Shalat" }}</Button>
            </div>"""

prayer_recurrence = """<div>
              <label class="mb-1.5 block text-sm font-medium text-slate-900">Perulangan (Otomatis)</label>
              <Select v-model="prayerForm.recurrence">
                <option value="none">Satu Kali Saja (Tidak Berulang)</option>
                <option value="daily">Harian (Selama 30 Hari)</option>
                <option value="weekly">Mingguan (Selama 4 Minggu)</option>
                <option value="monthly">Bulanan (Selama 3 Bulan)</option>
              </Select>
            </div>"""

content = content.replace(prayer_submit_div, prayer_recurrence + '\n            ' + prayer_submit_div)


service_submit_div = """<div class="flex items-end">
              <Button :disabled="serviceForm.processing" @click="submitService">{{ serviceForm.processing ? "Menyimpan..." : "Simpan Jadwal Petugas" }}</Button>
            </div>"""

service_recurrence = """<div>
              <label class="mb-1.5 block text-sm font-medium text-slate-900">Perulangan (Otomatis)</label>
              <Select v-model="serviceForm.recurrence">
                <option value="none">Satu Kali Saja (Tidak Berulang)</option>
                <option value="daily">Harian (Selama 30 Hari)</option>
                <option value="weekly">Mingguan (Selama 4 Minggu)</option>
                <option value="monthly">Bulanan (Selama 3 Bulan)</option>
              </Select>
            </div>"""

content = content.replace(service_submit_div, service_recurrence + '\n            ' + service_submit_div)

# Change title of dialog to match active form
content = content.replace('<div class="p-5 max-h-[85vh] overflow-y-auto">', '<div class="p-5 max-h-[85vh] overflow-y-auto">\n        <h2 class="text-xl font-bold mb-4">{{ activeFormType === "prayer" ? "Tambah Jadwal Shalat" : "Tambah Jadwal Petugas" }}</h2>')

# Remove the text titles from the cards to avoid duplication
content = content.replace('<p class="text-sm font-semibold text-slate-900">Jadwal Shalat / Khatib</p>', '')
content = content.replace('<p class="text-sm font-semibold text-slate-900">Jadwal Petugas</p>', '')

# Let's also remove the <hr class="my-6 border-slate-200" /> since we only show one form
content = content.replace('<hr class="my-6 border-slate-200" />', '')

with open('/Users/user/Work/masjid-system/resources/js/Pages/Admin/Schedules/Index.vue', 'w') as f:
    f.write(content)

