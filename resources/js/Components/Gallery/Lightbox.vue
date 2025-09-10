<template>
  <Teleport to="body">
    <div
      class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-90"
      @click="handleBackdropClick"
      @keydown="handleKeydown"
      tabindex="0"
      ref="lightboxRef"
    >
      <!-- Close button -->
      <button
        @click="$emit('close')"
        class="absolute top-4 right-4 z-10 p-2 text-white hover:text-gray-300 transition-colors"
      >
        <XMarkIcon class="h-6 w-6" />
      </button>

      <!-- Navigation arrows -->
      <button
        v-if="images.length > 1 && currentIndex > 0"
        @click="$emit('prev')"
        class="absolute left-4 top-1/2 transform -translate-y-1/2 z-10 p-2 text-white hover:text-gray-300 transition-colors"
      >
        <ChevronLeftIcon class="h-8 w-8" />
      </button>

      <button
        v-if="images.length > 1 && currentIndex < images.length - 1"
        @click="$emit('next')"
        class="absolute right-4 top-1/2 transform -translate-y-1/2 z-10 p-2 text-white hover:text-gray-300 transition-colors"
      >
        <ChevronRightIcon class="h-8 w-8" />
      </button>

      <!-- Image counter -->
      <div v-if="images.length > 1" class="absolute top-4 left-4 z-10 text-white text-sm">
        {{ currentIndex + 1 }} of {{ images.length }}
      </div>

      <!-- Main content -->
      <div class="flex flex-col h-full w-full max-w-7xl mx-auto p-4">
        <!-- Image -->
        <div class="flex-1 flex items-center justify-center">
          <img
            :src="currentImage.versions?.find(v => v.variant === 'large')?.url || currentImage.url"
            :alt="currentImage.alt_text || currentImage.title"
            class="max-w-full max-h-full object-contain"
            @click.stop
          >
        </div>

        <!-- Image info -->
        <div class="mt-4 text-white">
          <div class="flex items-start justify-between">
            <div class="flex-1 min-w-0">
              <h2 class="text-lg font-semibold truncate">
                {{ currentImage.title || 'Untitled' }}
              </h2>
              <p v-if="currentImage.caption" class="mt-1 text-sm text-gray-300">
                {{ currentImage.caption }}
              </p>
              <div class="mt-2 flex items-center space-x-4 text-xs text-gray-400">
                <span>{{ formatDate(currentImage.created_at) }}</span>
                <span v-if="currentImage.dimensions">{{ currentImage.dimensions }}</span>
                <span v-if="currentImage.formatted_size">{{ currentImage.formatted_size }}</span>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center space-x-2 ml-4">
              <button
                v-if="currentImage.can?.download"
                @click="downloadImage"
                class="p-2 text-white hover:text-gray-300 transition-colors"
                title="Download"
              >
                <ArrowDownTrayIcon class="h-5 w-5" />
              </button>
              
              <button
                v-if="currentImage.can?.edit"
                @click="editImage"
                class="p-2 text-white hover:text-gray-300 transition-colors"
                title="Edit"
              >
                <PencilIcon class="h-5 w-5" />
              </button>

              <button
                @click="toggleLike"
                class="p-2 transition-colors"
                :class="isLiked ? 'text-red-500' : 'text-white hover:text-gray-300'"
                title="Like"
              >
                <HeartIcon class="h-5 w-5" :class="isLiked ? 'fill-current' : ''" />
              </button>

              <Dropdown align="right" width="48">
                <template #trigger>
                  <button class="p-2 text-white hover:text-gray-300 transition-colors">
                    <EllipsisVerticalIcon class="h-5 w-5" />
                  </button>
                </template>

                <template #content>
                  <DropdownLink :href="route('images.show', currentImage.slug)">
                    View Details
                  </DropdownLink>
                  <DropdownLink
                    v-if="currentImage.can?.edit"
                    :href="route('images.edit', currentImage.slug)"
                  >
                    Edit Image
                  </DropdownLink>
                  <div class="border-t border-gray-100"></div>
                  <DropdownLink href="#" @click="copyLink">
                    Copy Link
                  </DropdownLink>
                  <DropdownLink href="#" @click="shareImage">
                    Share
                  </DropdownLink>
                </template>
              </Dropdown>
            </div>
          </div>

          <!-- Tags -->
          <div v-if="currentImage.tags && currentImage.tags.length" class="mt-3">
            <div class="flex flex-wrap gap-2">
              <Link
                v-for="tag in currentImage.tags"
                :key="tag.id"
                :href="route('tags.show', tag.slug)"
                class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-gray-700 text-gray-300 hover:bg-gray-600 transition-colors"
              >
                {{ tag.name }}
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import route from 'ziggy-js'
import {
  XMarkIcon,
  ChevronLeftIcon,
  ChevronRightIcon,
  ArrowDownTrayIcon,
  PencilIcon,
  HeartIcon,
  EllipsisVerticalIcon,
} from '@heroicons/vue/24/outline'

import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'

const props = defineProps({
  images: {
    type: Array,
    required: true,
  },
  initialIndex: {
    type: Number,
    default: 0,
  },
})

const emit = defineEmits(['close', 'next', 'prev'])

const lightboxRef = ref(null)
const currentIndex = ref(props.initialIndex)
const isLiked = ref(false)

const currentImage = computed(() => {
  return props.images[currentIndex.value]
})

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}

const handleBackdropClick = (event) => {
  if (event.target === event.currentTarget) {
    emit('close')
  }
}

const handleKeydown = (event) => {
  switch (event.key) {
    case 'Escape':
      emit('close')
      break
    case 'ArrowLeft':
      if (currentIndex.value > 0) {
        emit('prev')
        currentIndex.value--
      }
      break
    case 'ArrowRight':
      if (currentIndex.value < props.images.length - 1) {
        emit('next')
        currentIndex.value++
      }
      break
  }
}

const downloadImage = () => {
  window.open(route('images.download', currentImage.value.slug), '_blank')
}

const editImage = () => {
  window.location = route('images.edit', currentImage.value.slug)
}

const toggleLike = async () => {
  try {
    const response = await router.post(
      route('images.like', currentImage.value.slug),
      {},
      {
        preserveState: true,
        preserveScroll: true,
      }
    )
    isLiked.value = !isLiked.value
  } catch (error) {
    console.error('Failed to toggle like:', error)
  }
}

const copyLink = () => {
  const url = window.location.origin + route('images.show', currentImage.value.slug)
  navigator.clipboard.writeText(url).then(() => {
    // Show notification
    console.log('Link copied to clipboard')
  })
}

const shareImage = () => {
  if (navigator.share) {
    navigator.share({
      title: currentImage.value.title,
      text: currentImage.value.caption,
      url: window.location.origin + route('images.show', currentImage.value.slug),
    })
  } else {
    copyLink()
  }
}

onMounted(() => {
  nextTick(() => {
    lightboxRef.value?.focus()
  })
})

onUnmounted(() => {
  // Cleanup if needed
})
</script>
