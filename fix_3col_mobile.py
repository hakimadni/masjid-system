import re

with open('/Users/user/Work/masjid-system/resources/js/Pages/Admin/Dashboard.vue', 'r') as f:
    content = f.read()

new_grid = """
      <!-- Action-Oriented Grid (3 per row Mobile) -->
      <div>
        <p class="text-sm font-semibold text-slate-500 mb-3 uppercase tracking-wider">Aksi Cepat</p>
        <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-2 sm:gap-4">
          
          <Link :href="route('finance.index')" class="col-span-1 group relative flex flex-col items-center justify-start gap-2 overflow-hidden rounded-[1.25rem] bg-white p-3 sm:p-4 shadow-sm border border-slate-100 hover:shadow-md hover:border-emerald-200 transition-all active:scale-95">
            <div class="rounded-2xl bg-emerald-100 p-3 sm:p-4 text-emerald-600">
              <svg class="h-6 w-6 sm:h-7 sm:w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
            </div>
            <span class="text-[11px] sm:text-xs font-bold text-slate-700 text-center leading-tight">Uang Masuk</span>
          </Link>

          <Link :href="route('finance.index')" class="col-span-1 group relative flex flex-col items-center justify-start gap-2 overflow-hidden rounded-[1.25rem] bg-white p-3 sm:p-4 shadow-sm border border-slate-100 hover:shadow-md hover:border-rose-200 transition-all active:scale-95">
            <div class="rounded-2xl bg-rose-100 p-3 sm:p-4 text-rose-600">
              <svg class="h-6 w-6 sm:h-7 sm:w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4" /></svg>
            </div>
            <span class="text-[11px] sm:text-xs font-bold text-slate-700 text-center leading-tight">Uang Keluar</span>
          </Link>

          <Link :href="route('donations.index')" class="col-span-1 group relative flex flex-col items-center justify-start gap-2 overflow-hidden rounded-[1.25rem] bg-white p-3 sm:p-4 shadow-sm border border-slate-100 hover:shadow-md hover:border-blue-200 transition-all active:scale-95">
            <div class="rounded-2xl bg-blue-100 p-3 sm:p-4 text-blue-600">
              <svg class="h-6 w-6 sm:h-7 sm:w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
            </div>
            <span class="text-[11px] sm:text-xs font-bold text-slate-700 text-center leading-tight">Donasi ZISWAF</span>
          </Link>

          <Link :href="route('qurban.index')" class="col-span-1 group relative flex flex-col items-center justify-start gap-2 overflow-hidden rounded-[1.25rem] bg-white p-3 sm:p-4 shadow-sm border border-slate-100 hover:shadow-md hover:border-amber-200 transition-all active:scale-95">
            <div class="rounded-2xl bg-amber-100 p-3 sm:p-4 text-amber-600">
              <svg class="h-6 w-6 sm:h-7 sm:w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" /></svg>
            </div>
            <span class="text-[11px] sm:text-xs font-bold text-slate-700 text-center leading-tight">Program Qurban</span>
          </Link>

          <Link :href="route('schedules.index')" class="col-span-1 group relative flex flex-col items-center justify-start gap-2 overflow-hidden rounded-[1.25rem] bg-white p-3 sm:p-4 shadow-sm border border-slate-100 hover:shadow-md hover:border-sky-200 transition-all active:scale-95">
            <div class="rounded-2xl bg-sky-100 p-3 sm:p-4 text-sky-600">
              <svg class="h-6 w-6 sm:h-7 sm:w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            </div>
            <span class="text-[11px] sm:text-xs font-bold text-slate-700 text-center leading-tight">Jadwal Imam</span>
          </Link>

          <Link :href="route('announcements.index')" class="col-span-1 group relative flex flex-col items-center justify-start gap-2 overflow-hidden rounded-[1.25rem] bg-white p-3 sm:p-4 shadow-sm border border-slate-100 hover:shadow-md hover:border-indigo-200 transition-all active:scale-95">
            <div class="rounded-2xl bg-indigo-100 p-3 sm:p-4 text-indigo-600">
              <svg class="h-6 w-6 sm:h-7 sm:w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" /></svg>
            </div>
            <span class="text-[11px] sm:text-xs font-bold text-slate-700 text-center leading-tight">Buat Info</span>
          </Link>

          <Link :href="route('events.index')" class="col-span-1 group relative flex flex-col items-center justify-start gap-2 overflow-hidden rounded-[1.25rem] bg-white p-3 sm:p-4 shadow-sm border border-slate-100 hover:shadow-md hover:border-purple-200 transition-all active:scale-95">
            <div class="rounded-2xl bg-purple-100 p-3 sm:p-4 text-purple-600">
              <svg class="h-6 w-6 sm:h-7 sm:w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
            </div>
            <span class="text-[11px] sm:text-xs font-bold text-slate-700 text-center leading-tight">Agenda</span>
          </Link>

          <Link :href="route('jamaahs.index')" class="col-span-1 group relative flex flex-col items-center justify-start gap-2 overflow-hidden rounded-[1.25rem] bg-white p-3 sm:p-4 shadow-sm border border-slate-100 hover:shadow-md hover:border-teal-200 transition-all active:scale-95">
            <div class="rounded-2xl bg-teal-100 p-3 sm:p-4 text-teal-600">
              <svg class="h-6 w-6 sm:h-7 sm:w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" /></svg>
            </div>
            <span class="text-[11px] sm:text-xs font-bold text-slate-700 text-center leading-tight">Data Jamaah</span>
          </Link>
          
          <Link :href="route('assets.index')" class="col-span-1 group relative flex flex-col items-center justify-start gap-2 overflow-hidden rounded-[1.25rem] bg-white p-3 sm:p-4 shadow-sm border border-slate-100 hover:shadow-md hover:border-orange-200 transition-all active:scale-95">
            <div class="rounded-2xl bg-orange-100 p-3 sm:p-4 text-orange-600">
              <svg class="h-6 w-6 sm:h-7 sm:w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
            </div>
            <span class="text-[11px] sm:text-xs font-bold text-slate-700 text-center leading-tight">Aset Masjid</span>
          </Link>
        </div>
      </div>
"""

# Extract the previous bento grid and replace it
start_idx = content.find('<!-- Action-Oriented Bento Grid (Main Entry Points) -->')
if start_idx != -1:
    end_idx = content.find('<!-- Summary Data / Stat Cards -->')
    if end_idx != -1:
        content = content[:start_idx] + new_grid + content[end_idx:]

with open('/Users/user/Work/masjid-system/resources/js/Pages/Admin/Dashboard.vue', 'w') as f:
    f.write(content)
