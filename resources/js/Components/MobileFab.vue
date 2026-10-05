<template>
    <div class="fixed right-4 z-50 md:hidden" :style="{ bottom: 'calc(5rem + env(safe-area-inset-bottom))' }">
        <slot>
            <button @click="$emit('click')" class="flex h-14 w-14 items-center justify-center rounded-full bg-white/80 backdrop-blur-md border border-white text-emerald-600 shadow-xl shadow-slate-900/10 ring-1 ring-slate-200/50 active:scale-95 transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
            </button>
        </slot>
    </div>
</template>

<script setup>
defineEmits(['click']);
</script>