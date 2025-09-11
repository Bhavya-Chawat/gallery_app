<template>
  <div class="relative">
    <div @click="open = !open">
      <slot name="trigger" />
    </div>

    <!-- Full Screen Dropdown Overlay -->
    <div v-show="open" class="fixed inset-0 z-40" @click="open = false" />

    <!-- Dropdown Panel -->
    <transition
      enter-active-class="transition ease-out duration-200"
      enter-from-class="transform opacity-0 scale-95"
      enter-to-class="transform opacity-100 scale-100"
      leave-active-class="transition ease-in duration-75"
      leave-from-class="transform opacity-100 scale-100"
      leave-to-class="transform opacity-0 scale-95"
    >
      <div
        v-show="open"
        class="absolute z-50 mt-2 rounded-xl shadow-xl border border-gray-200 dark:border-slate-600"
        :class="[widthClass, alignmentClasses]"
        style="display: none"
        @click="open = false"
      >
        <div class="rounded-xl py-1 bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl ring-1 ring-black ring-opacity-5">
          <slot name="content" />
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  align: {
    type: String,
    default: 'right',
    validator: (value) => ['left', 'right'].includes(value)
  },
  width: {
    type: String,
    default: '48',
    validator: (value) => ['48', '56', '64', '72', '80'].includes(value)
  },
  contentClasses: {
    type: String,
    default: 'py-1 bg-white/70 dark:bg-slate-800/70'
  }
})

const open = ref(false)

const widthClass = computed(() => {
  const widthMap = {
    '48': 'w-48',
    '56': 'w-56', 
    '64': 'w-64',
    '72': 'w-72',
    '80': 'w-80'
  }
  return widthMap[props.width] || 'w-48'
})

const alignmentClasses = computed(() => {
  if (props.align === 'left') {
    return 'ltr:origin-top-left rtl:origin-top-right start-0'
  }
  
  if (props.align === 'right') {
    return 'ltr:origin-top-right rtl:origin-top-left end-0'
  }
  
  return 'ltr:origin-top-right rtl:origin-top-left end-0'
})

const closeOnEscape = (e) => {
  if (open.value && e.key === 'Escape') {
    open.value = false
  }
}

onMounted(() => {
  document.addEventListener('keydown', closeOnEscape)
})

onUnmounted(() => {
  document.removeEventListener('keydown', closeOnEscape)
})
</script>