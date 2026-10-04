import re

with open('/Users/user/Work/masjid-system/resources/js/Pages/Admin/Dashboard.vue', 'r') as f:
    content = f.read()

# Add a Quick Action section right after the StatCards
quick_actions_html = """      <!-- Mobile-First Action Grid (Marbot Mode) -->
      <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 lg:hidden">
        <Link :href="route('finance.transactions.create', { type: 'income' })" class="group relative flex flex-col items-center gap-3 overflow-hidden rounded-3xl bg-emerald-600 p-5 text-center text-white shadow-lg shadow-emerald-900/20 active:scale-95 transition-all">
          <div class="rounded-2xl bg-white/20 p-3 backdrop-blur-md">
            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
          </div>
          <span class="text-sm font-semibold tracking-wide">Uang Masuk</span>
        </Link>
        <Link :href="route('finance.transactions.create', { type: 'expense' })" class="group relative flex flex-col items-center gap-3 overflow-hidden rounded-3xl bg-rose-500 p-5 text-center text-white shadow-lg shadow-rose-900/20 active:scale-95 transition-all">
          <div class="rounded-2xl bg-white/20 p-3 backdrop-blur-md">
            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4" /></svg>
          </div>
          <span class="text-sm font-semibold tracking-wide">Uang Keluar</span>
        </Link>
        <Link :href="route('announcements.create')" class="group relative flex flex-col items-center gap-3 overflow-hidden rounded-3xl bg-amber-500 p-5 text-center text-white shadow-lg shadow-amber-900/20 active:scale-95 transition-all">
          <div class="rounded-2xl bg-white/20 p-3 backdrop-blur-md">
            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" /></svg>
          </div>
          <span class="text-sm font-semibold tracking-wide">Pengumuman</span>
        </Link>
        <Link :href="route('prayer-schedules.index')" class="group relative flex flex-col items-center gap-3 overflow-hidden rounded-3xl bg-sky-500 p-5 text-center text-white shadow-lg shadow-sky-900/20 active:scale-95 transition-all">
          <div class="rounded-2xl bg-white/20 p-3 backdrop-blur-md">
            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
          </div>
          <span class="text-sm font-semibold tracking-wide">Jadwal Imam</span>
        </Link>
      </div>
"""

# Replace <div class="grid gap-6 xl:grid-cols-2"> with <div class="hidden md:grid gap-6 xl:grid-cols-2">
content = content.replace('<div class="grid gap-6 xl:grid-cols-2">', '<div class="hidden md:grid gap-6 xl:grid-cols-2">')

# Inject quick actions right before charts
content = content.replace('<!-- Charts Section -->', quick_actions_html + '\n      <!-- Charts Section (Desktop Only) -->')

# Change StatCard grid to be horizontal scrollable on mobile
content = content.replace(
    '<div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">',
    '<div class="flex gap-4 overflow-x-auto pb-4 md:grid md:grid-cols-2 xl:grid-cols-3 snap-x snap-mandatory hide-scrollbar">'
)

# Ensure StatCard has flex-shrink-0 for mobile scrolling
content = content.replace(
    '<StatCard v-for="card in cards" :key="card.label" :title="card.label" :value="formatValue(card)" />',
    '<div class="min-w-[280px] snap-center shrink-0 md:w-auto"><StatCard v-for="card in cards" :key="card.label" :title="card.label" :value="formatValue(card)" /></div>'
)
# Ah wait, putting it inside the v-for is wrong, the div should HAVE the v-for. Let's fix that.
content = content.replace(
    '<div class="min-w-[280px] snap-center shrink-0 md:w-auto"><StatCard v-for="card in cards" :key="card.label" :title="card.label" :value="formatValue(card)" /></div>',
    '<div v-for="card in cards" :key="card.label" class="min-w-[85vw] sm:min-w-[280px] snap-center shrink-0 md:min-w-0 md:w-auto"><StatCard :title="card.label" :value="formatValue(card)" /></div>'
)


with open('/Users/user/Work/masjid-system/resources/js/Pages/Admin/Dashboard.vue', 'w') as f:
    f.write(content)
