import re

with open('/Users/user/Work/masjid-system/resources/js/Pages/Admin/Finance/Index.vue', 'r') as f:
    content = f.read()

# Change terminology globally in labels
content = content.replace('Pemasukan', 'Uang Masuk')
content = content.replace('Pengeluaran', 'Uang Keluar')
content = content.replace('value="income">Income', 'value="income">Uang Masuk')
content = content.replace('value="expense">Expense', 'value="expense">Uang Keluar')

# Upgrade mobile-card slot for Finance to look like an ATM / Ledger card (Laymen friendly)
old_mobile_card = """<template #mobile-card="{ item }">
                <div class="flex items-center justify-between border-b border-slate-100 bg-white p-4 rounded-xl shadow-sm mb-2">
                  <div>
                    <p class="font-medium text-slate-900">{{ item.title }}</p>
                    <p class="text-xs text-slate-500">{{ item.category }} • {{ item.transaction_date }}</p>
                    <p class="mt-1 font-medium text-slate-900"><MoneyDisplay :value="item.amount" /></p>
                    <p class="text-xs text-slate-500">{{ item.entry_type === 'income' ? 'Uang Masuk' : 'Uang Keluar' }}</p>
                  </div>
                  <div class="flex flex-col items-end gap-2">
                    <StatusBadge :status="item.status" />
                    <ActionMenu :items="actionItems(item)" @select="handleAction(item, $event)" />
                  </div>
                </div>
              </template>"""

new_mobile_card = """<template #mobile-card="{ item }">
                <div class="group relative flex flex-col justify-between overflow-hidden rounded-3xl border border-emerald-900/5 bg-white shadow-lg shadow-emerald-900/5 ring-1 ring-slate-100/50 mb-3 p-4 transition-all active:scale-95">
                  <div class="flex items-center justify-between">
                     <div class="flex items-center gap-3">
                        <div :class="[
                           'flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl',
                           item.entry_type === 'income' ? 'bg-emerald-100 text-emerald-600' : 'bg-rose-100 text-rose-600'
                        ]">
                          <svg v-if="item.entry_type === 'income'" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3" /></svg>
                          <svg v-else class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" /></svg>
                        </div>
                        <div class="min-w-0 pr-2">
                          <p class="font-bold text-slate-800 text-sm leading-tight truncate">{{ item.title }}</p>
                          <p class="text-xs text-slate-500 font-medium mt-1 truncate">{{ item.category }} • {{ item.transaction_date }}</p>
                        </div>
                     </div>
                     <div class="text-right shrink-0">
                        <p :class="['font-bold whitespace-nowrap text-sm', item.entry_type === 'income' ? 'text-emerald-600' : 'text-slate-800']">
                          {{ item.entry_type === 'income' ? '+' : '-' }} <MoneyDisplay :value="item.amount" />
                        </p>
                        <div class="flex justify-end mt-1 items-center gap-1">
                          <StatusBadge size="sm" :status="item.status" />
                          <ActionMenu :items="actionItems(item)" @select="handleAction(item, $event)" />
                        </div>
                     </div>
                  </div>
                </div>
              </template>"""

content = content.replace(old_mobile_card, new_mobile_card)

# Update StatCard horizontal scroll wrapper
content = content.replace(
    '<div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">',
    '<div class="flex gap-4 overflow-x-auto pb-4 md:grid md:grid-cols-2 xl:grid-cols-4 snap-x snap-mandatory hide-scrollbar">'
)

content = content.replace(
    '<StatCard',
    '<div class="min-w-[85vw] sm:min-w-[280px] snap-center shrink-0 md:min-w-0 md:w-auto"><StatCard'
)
content = content.replace(
    '</StatCard>',
    '</StatCard></div>'
)

with open('/Users/user/Work/masjid-system/resources/js/Pages/Admin/Finance/Index.vue', 'w') as f:
    f.write(content)

