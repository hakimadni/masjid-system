import re

with open('/Users/user/Work/masjid-system/resources/js/Pages/Admin/Finance/Index.vue', 'r') as f:
    content = f.read()

# Replace the flex row alignment from items-center to items-start, and remove truncate
old_block = """<div class="flex items-center justify-between">
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
                     <div class="text-right shrink-0">"""

new_block = """<div class="flex items-start justify-between gap-2">
                     <div class="flex items-start gap-3 min-w-0">
                        <div :class="[
                           'flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl',
                           item.entry_type === 'income' ? 'bg-emerald-100 text-emerald-600' : 'bg-rose-100 text-rose-600'
                        ]">
                          <svg v-if="item.entry_type === 'income'" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3" /></svg>
                          <svg v-else class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" /></svg>
                        </div>
                        <div class="min-w-0 pt-0.5">
                          <p class="font-bold text-slate-800 text-sm leading-snug break-words line-clamp-2">{{ item.title }}</p>
                          <p class="text-xs text-slate-500 font-medium mt-1 break-words line-clamp-2">{{ item.category }}<br/><span class="text-[10px] text-slate-400">{{ item.transaction_date }}</span></p>
                        </div>
                     </div>
                     <div class="text-right shrink-0 flex flex-col items-end">"""

content = content.replace(old_block, new_block)

with open('/Users/user/Work/masjid-system/resources/js/Pages/Admin/Finance/Index.vue', 'w') as f:
    f.write(content)

