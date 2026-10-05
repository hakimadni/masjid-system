import re

# 1. FIX DIALOG.VUE TO EMIT 'close' and add an explicit close button
with open('/Users/user/Work/masjid-system/resources/js/Components/ui/dialog/Dialog.vue', 'r') as f:
    dialog_content = f.read()

# Add 'close' to emits
dialog_content = dialog_content.replace('defineEmits(["update:open"])', 'defineEmits(["update:open", "close"])')

# Update computed setter
dialog_content = dialog_content.replace(
    'set: (value) => emit("update:open", value),',
    'set: (value) => { emit("update:open", value); if (!value) emit("close"); },'
)

# Add close button inside the modal wrapper
close_btn = """
        <div class="w-full max-w-md rounded-2xl bg-white shadow-2xl overflow-hidden relative">
          <!-- Close button -->
          <button @click="isOpen = false" class="absolute top-4 right-4 z-50 rounded-full bg-slate-100 p-2 text-slate-500 hover:bg-slate-200 active:scale-95 transition-all">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
          </button>
          <slot :close="() => (isOpen = false)" />
        </div>
"""
dialog_content = re.sub(
    r'<div class="w-full max-w-md rounded-lg bg-white shadow-xl">.*?<slot :close="\(\) => \(isOpen = false\)" \/>.*?<\/div>',
    close_btn,
    dialog_content,
    flags=re.DOTALL
)

with open('/Users/user/Work/masjid-system/resources/js/Components/ui/dialog/Dialog.vue', 'w') as f:
    f.write(dialog_content)


# 2. FIX MOBILEFAB.VUE TO BE GLASSMORPHISM
with open('/Users/user/Work/masjid-system/resources/js/Components/MobileFab.vue', 'r') as f:
    fab_content = f.read()

fab_content = fab_content.replace(
    'bg-primary text-primary-foreground shadow-lg transition-colors hover:bg-primary/90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50',
    'bg-white/80 backdrop-blur-md border border-white text-emerald-600 shadow-xl shadow-slate-900/10 ring-1 ring-slate-200/50 active:scale-95 transition-all'
)
with open('/Users/user/Work/masjid-system/resources/js/Components/MobileFab.vue', 'w') as f:
    f.write(fab_content)


# 3. FIX SPEEDDIALFAB.VUE TO MATCH
with open('/Users/user/Work/masjid-system/resources/js/Components/ui/button/SpeedDialFab.vue', 'r') as f:
    speed_content = f.read()

speed_content = speed_content.replace(
    "isOpen ? 'bg-rose-500 rotate-45' : 'bg-emerald-600 active:scale-95'",
    "isOpen ? 'bg-rose-500 text-white rotate-45 border-rose-500 shadow-rose-900/20' : 'bg-white/80 backdrop-blur-md border-white text-emerald-600 shadow-slate-900/10 ring-1 ring-slate-200/50 active:scale-95'"
)
speed_content = speed_content.replace(
    "class=\"z-50 flex h-14 w-14 items-center justify-center rounded-full text-white shadow-lg transition-all duration-300\"",
    "class=\"z-50 flex h-14 w-14 items-center justify-center rounded-full shadow-xl transition-all duration-300 border\""
)
with open('/Users/user/Work/masjid-system/resources/js/Components/ui/button/SpeedDialFab.vue', 'w') as f:
    f.write(speed_content)

