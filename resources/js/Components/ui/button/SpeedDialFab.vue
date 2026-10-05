<script setup>
import { ref } from 'vue';
import { Plus, X } from 'lucide-vue-next';

const props = defineProps({
  options: {
    type: Array,
    required: true,
    // Example: [{ key: 'prayer', label: 'Jadwal Shalat', icon: '...' }]
  }
});

const emit = defineEmits(['select']);
const isOpen = ref(false);

const toggle = () => {
  isOpen.value = !isOpen.value;
};

const selectOption = (key) => {
  isOpen.value = false;
  emit('select', key);
};
</script>

<template>
  <div class="fixed bottom-24 right-5 z-50 md:hidden flex flex-col items-end gap-3">
    <!-- Overlay to close when clicking outside -->
    <div v-if="isOpen" @click="isOpen = false" class="fixed inset-0 z-40 bg-slate-900/20 backdrop-blur-sm transition-opacity"></div>

    <!-- Options Menu -->
    <Transition
      enter-active-class="transition ease-out duration-200"
      enter-from-class="opacity-0 translate-y-4 scale-95"
      enter-to-class="opacity-100 translate-y-0 scale-100"
      leave-active-class="transition ease-in duration-150"
      leave-from-class="opacity-100 translate-y-0 scale-100"
      leave-to-class="opacity-0 translate-y-4 scale-95"
    >
      <div v-if="isOpen" class="z-50 flex flex-col items-end gap-3 mb-2">
        <button
          v-for="opt in options"
          :key="opt.key"
          @click="selectOption(opt.key)"
          class="flex items-center gap-3 rounded-full bg-white px-4 py-3 text-sm font-semibold text-slate-700 shadow-xl shadow-slate-900/10 hover:bg-slate-50 border border-slate-100"
        >
          {{ opt.label }}
        </button>
      </div>
    </Transition>

    <!-- Main Button -->
    <button
      @click="toggle"
      :class="[
        'z-50 flex h-14 w-14 items-center justify-center rounded-full text-white shadow-lg transition-all duration-300',
        isOpen ? 'bg-rose-500 rotate-45' : 'bg-emerald-600 active:scale-95'
      ]"
    >
      <Plus class="h-6 w-6" />
    </button>
  </div>
</template>
