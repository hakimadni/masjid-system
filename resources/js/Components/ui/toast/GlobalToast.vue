<script setup>
import { computed, watch, ref, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const visible = ref(false);
const message = ref('');
const type = ref('success'); // 'success' or 'error'
let timeout = null;

const showToast = (msg, msgType = 'success') => {
    message.value = msg;
    type.value = msgType;
    visible.value = true;
    
    if (timeout) clearTimeout(timeout);
    timeout = setTimeout(() => {
        visible.value = false;
    }, 4000);
};

// Watch for Inertia page flashes
watch(
    () => page.props.flash,
    (flash) => {
        if (flash?.success) {
            showToast(flash.success, 'success');
        } else if (flash?.error || flash?.danger) {
            showToast(flash.error || flash.danger, 'error');
        }
    },
    { deep: true, immediate: true }
);

// We can also watch errors object for generic errors (Wait, errors are usually inline, but we can catch generic ones)
// We already have the Axios interceptor for network errors.
</script>

<template>
  <Transition
    enter-active-class="transform ease-out duration-300 transition"
    enter-from-class="translate-y-4 opacity-0 sm:translate-y-0 sm:translate-x-2"
    enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
    leave-active-class="transition ease-in duration-200"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <div v-if="visible" class="fixed top-4 left-1/2 -translate-x-1/2 z-[100] w-full max-w-sm px-4 sm:px-0 sm:top-6">
      <div 
        :class="[
          'pointer-events-auto flex items-center gap-3 overflow-hidden rounded-2xl p-4 shadow-xl shadow-slate-900/10 ring-1 backdrop-blur-xl',
          type === 'success' ? 'bg-emerald-600/95 ring-emerald-500/50 text-white' : 'bg-rose-600/95 ring-rose-500/50 text-white'
        ]"
      >
        <div class="flex-shrink-0">
          <svg v-if="type === 'success'" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <svg v-else class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
        </div>
        <div class="flex-1 w-0">
          <p class="text-sm font-semibold">{{ message }}</p>
        </div>
        <div class="flex-shrink-0">
          <button @click="visible = false" class="inline-flex rounded-md p-1.5 focus:outline-none focus:ring-2 focus:ring-white/50 hover:bg-white/20 transition-colors">
            <span class="sr-only">Tutup</span>
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
    </div>
  </Transition>
</template>
