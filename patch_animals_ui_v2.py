import re

with open('/Users/user/Work/masjid-system/resources/js/Pages/Animals/Index.vue', 'r') as f:
    content = f.read()

# Add a mobile list block right before the table
mobile_list_html = """
          <!-- Mobile List -->
          <div class="block md:hidden space-y-4 mb-6">
            <div v-for="animal in animals.data" :key="animal.id" class="group relative flex flex-col overflow-hidden rounded-3xl border border-emerald-900/5 bg-white shadow-lg shadow-emerald-900/5 ring-1 ring-slate-100/50 p-4 active:scale-95 transition-all">
              <div class="flex items-center justify-between">
                <div>
                  <p class="font-bold text-slate-800 text-base leading-tight">Hewan #{{ animal.id }} - <span class="capitalize">{{ animal.type }}</span></p>
                  <p class="text-xs text-slate-500 font-medium mt-1">{{ animal.weight }} kg • Rp {{ Number(animal.price).toLocaleString('id-ID') }}</p>
                </div>
                <div class="text-right">
                  <AnimalStatusBadge :status="animal.status" />
                </div>
              </div>
              <div class="mt-4 flex gap-2">
                <Link :href="route('animals.show', animal.id)" class="flex-1 inline-flex justify-center items-center px-4 py-2 bg-emerald-100 text-emerald-700 font-semibold text-sm rounded-xl hover:bg-emerald-200">
                  Lihat Detail
                </Link>
              </div>
            </div>
            
            <div v-if="!animals.data || animals.data.length === 0" class="text-center py-8 text-slate-500">
              Belum ada data hewan
            </div>
          </div>
"""

content = content.replace('<table class="w-full text-left text-sm">', mobile_list_html + '\n          <table class="hidden md:table w-full text-left text-sm">')

with open('/Users/user/Work/masjid-system/resources/js/Pages/Animals/Index.vue', 'w') as f:
    f.write(content)
