<template>
  <teleport to="body">
    <div 
      v-if="show"
      class="fixed inset-0 z-50"
      role="dialog"
      aria-modal="true"
      aria-labelledby="lightbox-title"
      @click="handleBackdropClick"
      @keydown="handleKeydown"
      tabindex="-1"
      ref="lightboxContainer"
    >
      <!-- Backdrop -->
      <div 
        class="absolute inset-0 bg-black/90"
        @click="$emit('close')"
      ></div>

      <!-- Content -->
      <div class="relative z-10 h-full flex items-center justify-center">
        <!-- Close button -->
        <button 
          @click="$emit('close')"
          class="absolute top-4 right-4 text-white hover:text-gray-300"
        >
          <XMarkIcon class="w-8 h-8" />
        </button>

        <!-- Navigation -->
        <button 
          v-if="currentIndex > 0"
          @click="prev"
          class="absolute left-4 text-white hover:text-gray-300"
        >
          <ArrowLeftIcon class="w-8 h-8" />
        </button>

        <button 
          v-if="currentIndex < images.length - 1"
          @click="next"
          class="absolute right-4 text-white hover:text-gray-300"
        >
          <ArrowRightIcon class="w-8 h-8" />
        </button>

        <!-- Image -->
        <img 
          :src="images[currentIndex].url"
          :alt="images[currentIndex].caption || ''"
          class="max-h-[90vh] max-w-[90vw] object-contain"
        >
      </div>
    </div>
  </teleport>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { XMarkIcon, ArrowLeftIcon, ArrowRightIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  images: {
    type: Array,
    default: () => []
  },
  initialIndex: {
    type: Number,
    default: 0
  },
  show: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['close', 'change'])
const currentIndex = ref(props.initialIndex)

const next = () => {
  if (currentIndex.value < props.images.length - 1) {
    currentIndex.value++
    emit('change', currentIndex.value)
  }
}

const prev = () => {
  if (currentIndex.value > 0) {
    currentIndex.value--
    emit('change', currentIndex.value)
  }
}

const handleKeydown = (e) => {
  if (!props.show) return
  if (e.key === 'ArrowRight') next()
  if (e.key === 'ArrowLeft') prev()
  if (e.key === 'Escape') emit('close')
}

onMounted(() => {
  document.addEventListener('keydown', handleKeydown)
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleKeydown)
})
</script>