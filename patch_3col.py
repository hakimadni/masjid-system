import re

with open('/Users/user/Work/masjid-system/resources/js/Pages/Admin/Dashboard.vue', 'r') as f:
    content = f.read()

# Replace the wrapper
content = content.replace('<div class="grid grid-cols-4 gap-3 sm:gap-4">', '<div class="grid grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">')

# Modify Uang Masuk
content = content.replace(
    '''class="col-span-2 group relative flex flex-col justify-between overflow-hidden rounded-[2rem] bg-gradient-to-br from-emerald-500 to-emerald-700 p-5 sm:p-6 text-white shadow-xl shadow-emerald-900/10 active:scale-[0.98] transition-all border border-emerald-400/30 hover:shadow-emerald-900/20"''',
    '''class="col-span-1 group relative flex flex-col justify-between overflow-hidden rounded-[2rem] bg-gradient-to-br from-emerald-500 to-emerald-700 p-5 sm:p-6 text-white shadow-xl shadow-emerald-900/10 active:scale-[0.98] transition-all border border-emerald-400/30 hover:shadow-emerald-900/20"'''
)

# Modify Uang Keluar
content = content.replace(
    '''class="col-span-2 group relative flex flex-col justify-between overflow-hidden rounded-[2rem] bg-gradient-to-br from-rose-500 to-rose-700 p-5 sm:p-6 text-white shadow-xl shadow-rose-900/10 active:scale-[0.98] transition-all border border-rose-400/30 hover:shadow-rose-900/20"''',
    '''class="col-span-1 group relative flex flex-col justify-between overflow-hidden rounded-[2rem] bg-gradient-to-br from-rose-500 to-rose-700 p-5 sm:p-6 text-white shadow-xl shadow-rose-900/10 active:scale-[0.98] transition-all border border-rose-400/30 hover:shadow-rose-900/20"'''
)

# Modify Donasi & ZISWAF
content = content.replace(
    '''class="col-span-1 sm:col-span-2 group relative flex flex-col items-center justify-center gap-3 overflow-hidden rounded-[2rem] bg-white p-4 sm:p-6 text-slate-800 shadow-lg shadow-slate-200/50 active:scale-[0.98] transition-all border border-slate-100 hover:border-blue-200 hover:bg-blue-50/50"''',
    '''class="col-span-1 group relative flex flex-col items-center justify-center gap-3 overflow-hidden rounded-[2rem] bg-white p-4 sm:p-6 text-slate-800 shadow-lg shadow-slate-200/50 active:scale-[0.98] transition-all border border-slate-100 hover:border-blue-200 hover:bg-blue-50/50"'''
)

# Modify Program Qurban
content = content.replace(
    '''class="col-span-1 sm:col-span-2 group relative flex flex-col items-center justify-center gap-3 overflow-hidden rounded-[2rem] bg-white p-4 sm:p-6 text-slate-800 shadow-lg shadow-slate-200/50 active:scale-[0.98] transition-all border border-slate-100 hover:border-amber-200 hover:bg-amber-50/50"''',
    '''class="col-span-1 group relative flex flex-col items-center justify-center gap-3 overflow-hidden rounded-[2rem] bg-white p-4 sm:p-6 text-slate-800 shadow-lg shadow-slate-200/50 active:scale-[0.98] transition-all border border-slate-100 hover:border-amber-200 hover:bg-amber-50/50"'''
)

# Modify the smaller square items to match the aesthetic of Donasi (making them all uniform big squares)
content = content.replace(
    '''class="col-span-1 group relative flex flex-col items-center justify-center gap-2 overflow-hidden rounded-3xl bg-slate-50 p-4 text-slate-700 active:scale-[0.98] transition-all border border-slate-200/60 hover:bg-slate-100"''',
    '''class="col-span-1 group relative flex flex-col items-center justify-center gap-3 overflow-hidden rounded-[2rem] bg-white p-4 sm:p-6 text-slate-800 shadow-lg shadow-slate-200/50 active:scale-[0.98] transition-all border border-slate-100 hover:border-slate-300 hover:bg-slate-50/50"'''
)

# Adjust texts for uniform items
content = content.replace('text-[11px] sm:text-xs font-bold text-center leading-tight mt-1', 'text-xs sm:text-sm font-bold tracking-wide text-center mt-1')
content = content.replace('<br/>', ' ')

# Remove the old Aksi Cepat Harian Card that might be redundant
# Let's check if it exists:
old_aksi = """      <Card class="p-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
          <div>
            <p class="text-sm font-semibold text-slate-900">Aksi Cepat Harian</p>
            <p class="mt-1 text-sm text-slate-500">Akses modul yang paling sering dipakai pengurus masjid.</p>
          </div>
          <div class="flex flex-wrap gap-2">
            <Link :href="route('finance.index')"><Button variant="outline">Keuangan</Button></Link>
            <Link :href="route('donations.index')"><Button variant="outline">Donasi</Button></Link>
            <Link :href="route('schedules.index')"><Button variant="outline">Jadwal</Button></Link>
          </div>
        </div>
      </Card>"""
if old_aksi in content:
    content = content.replace(old_aksi, "")


with open('/Users/user/Work/masjid-system/resources/js/Pages/Admin/Dashboard.vue', 'w') as f:
    f.write(content)
