import re

with open('/Users/user/Work/masjid-system/resources/js/Pages/Animals/Index.vue', 'r') as f:
    content = f.read()

mobile_card = """<template #mobile-card="{ item }">
          <div class="group relative flex flex-col justify-between overflow-hidden rounded-3xl border border-emerald-900/5 bg-white shadow-lg shadow-emerald-900/5 ring-1 ring-slate-100/50 mb-3 p-4 transition-all active:scale-95">
            <div class="flex items-center justify-between">
              <div>
                <p class="font-bold text-slate-800 text-base leading-tight truncate">Hewan #{{ item.id }}</p>
                <p class="text-xs text-slate-500 font-medium mt-1 truncate">{{ item.animal_type }} • {{ item.animal_source }}</p>
              </div>
              <div class="text-right flex flex-col items-end gap-2">
                <AnimalStatusBadge :status="item.status" />
                <ActionMenu :items="[{key: 'show', label: 'Detail'}]" @select="router.visit(route('animals.show', item.id))" />
              </div>
            </div>
          </div>
        </template>
        <thead"""

content = content.replace('<thead', mobile_card)
content = content.replace('<DataTable>', '<DataTable v-if="animals.data.length" :data="animals.data">')

with open('/Users/user/Work/masjid-system/resources/js/Pages/Animals/Index.vue', 'w') as f:
    f.write(content)

