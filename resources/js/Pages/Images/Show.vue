<template>
  <AppLayout>
    <Head :title="image.title || 'Image'" />

    <div class="min-h-screen bg-gray-50">
      <!-- Navigation Bar -->
      <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex items-center justify-between h-16">
            <!-- Breadcrumb -->
            <nav class="flex" aria-label="Breadcrumb">
              <ol class="flex items-center space-x-4">
                <li>
                  <Link :href="route('gallery.index')" class="text-gray-400 hover:text-gray-500">
                    Gallery
                  </Link>
                </li>
                <li v-if="image.album" class="flex">
                  <ChevronRightIcon class="flex-shrink-0 h-5 w-5 text-gray-400" />
                  <Link :href="route('albums.show', image.album.slug)" class="ml-4 text-gray-400 hover:text-gray-500">
                    {{ image.album.title }}
                  </Link>
                </li>
                <li class="flex">
                  <ChevronRightIcon class="flex-shrink-0 h-5 w-5 text-gray-400" />
                  <span class="ml-4 text-sm font-medium text-gray-900 truncate max-w-xs">
                    {{ image.title || 'Untitled' }}
                  </span>
                </li>
              </ol>
            </nav>

            <!-- Actions -->
            <div class="flex items-center space-x-4">
              <button
                @click="toggleLike"
                class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                :class="{ 'text-red-600 border-red-300': isLiked }"
              >
                <HeartIcon class="h-4 w-4 mr-2" :class="{ 'fill-current': isLiked }" />
                {{ image.likes_count }}
              </button>

              <Dropdown align="right" width="48">
                <template #trigger>
                  <button class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <ShareIcon class="h-4 w-4 mr-2" />
                    Share
                    <ChevronDownIcon class="ml-2 h-4 w-4" />
                  </button>
                </template>

                <template #content>
                  <DropdownLink @click="copyLink">
                    Copy Link
                  </DropdownLink>
                  <DropdownLink @click="shareImage">
                    Share via...
                  </DropdownLink>
                  <div class="border-t border-gray-100" v-if="can.download"></div>
                  <DropdownLink @click="downloadImage" v-if="can.download">
                    Download Original
                  </DropdownLink>
                </template>
              </Dropdown>

              <Dropdown align="right" width="48" v-if="can.update || can.delete">
                <template #trigger>
                  <button class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <EllipsisVerticalIcon class="h-4 w-4" />
                  </button>
                </template>

                <template #content>
                  <DropdownLink :href="route('images.edit', image.slug)" v-if="can.update">
                    Edit Image
                  </DropdownLink>
                  <DropdownLink @click="togglePublish" v-if="can.update">
                    {{ image.is_published ? 'Unpublish' : 'Publish' }}
                  </DropdownLink>
                  <div class="border-t border-gray-100" v-if="can.update && can.delete"></div>
                  <DropdownLink @click="deleteImage" class="text-red-600" v-if="can.delete">
                    Delete Image
                  </DropdownLink>
                </template>
              </Dropdown>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content -->
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="lg:grid lg:grid-cols-3 lg:gap-8">
          <!-- Image Display -->
          <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
              <div class="relative">
                <img
                  :src="getImageUrl('large')"
                  :alt="image.alt_text || image.title"
                  class="w-full h-auto"
                  @click="openLightbox"
                >
                <button
                  @click="openLightbox"
                  class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-0 hover:bg-opacity-20 transition-all duration-200"
                >
                  <MagnifyingGlassIcon class="h-8 w-8 text-white opacity-0 hover:opacity-100 transition-opacity duration-200" />
                </button>
              </div>
            </div>

            <!-- Image Info -->
            <div class="mt-6 bg-white rounded-lg shadow-sm p-6">
              <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                  <h1 class="text-2xl font-bold text-gray-900">
                    {{ image.title || 'Untitled' }}
                  </h1>
                  <p v-if="image.caption" class="mt-2 text-gray-600">
                    {{ image.caption }}
                  </p>
                </div>
                <div class="ml-6 flex-shrink-0">
                  <UserAvatar :user="image.owner" size="lg" />
                </div>
              </div>

              <!-- Tags -->
              <div v-if="image.tags && image.tags.length" class="mt-4">
                <div class="flex flex-wrap gap-2">
                  <Link
                    v-for="tag in image.tags"
                    :key="tag.id"
                    :href="route('tags.show', tag.slug)"
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 hover:bg-blue-200"
                  >
                    {{ tag.name }}
                  </Link>
                </div>
              </div>

              <!-- Stats -->
              <div class="mt-6 grid grid-cols-3 gap-4 text-center">
                <div>
                  <div class="text-2xl font-semibold text-gray-900">{{ formatNumber(image.views_count) }}</div>
                  <div class="text-sm text-gray-500">Views</div>
                </div>
                <div>
                  <div class="text-2xl font-semibold text-gray-900">{{ formatNumber(image.likes_count) }}</div>
                  <div class="text-sm text-gray-500">Likes</div>
                </div>
                <div>
                  <div class="text-2xl font-semibold text-gray-900">{{ formatNumber(image.comments_count) }}</div>
                  <div class="text-sm text-gray-500">Comments</div>
                </div>
              </div>
            </div>

            <!-- Comments -->
            <div v-if="image.allow_comments" class="mt-6">
              <CommentsSection
                :image-id="image.id"
                :comments="comments"
                :can-comment="can.comment"
              />
            </div>
          </div>

          <!-- Sidebar -->
          <div class="mt-8 lg:mt-0">
            <!-- Image Details -->
            <div class="bg-white rounded-lg shadow-sm p-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Details</h3>
              <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                  <dt class="text-gray-500">Owner</dt>
                  <dd class="text-gray-900 font-medium">{{ image.owner.name }}</dd>
                </div>
                <div class="flex justify-between">
                  <dt class="text-gray-500">Uploaded</dt>
                  <dd class="text-gray-900">{{ formatDate(image.created_at) }}</dd>
                </div>
                <div class="flex justify-between">
                  <dt class="text-gray-500">Dimensions</dt>
                  <dd class="text-gray-900">{{ image.dimensions }}</dd>
                </div>
                <div class="flex justify-between">
                  <dt class="text-gray-500">File Size</dt>
                  <dd class="text-gray-900">{{ image.formatted_size }}</dd>
                </div>
                <div v-if="image.camera_make" class="flex justify-between">
                  <dt class="text-gray-500">Camera</dt>
                  <dd class="text-gray-900">{{ image.camera_make }} {{ image.camera_model }}</dd>
                </div>
                <div v-if="image.taken_at" class="flex justify-between">
                  <dt class="text-gray-500">Taken</dt>
                  <dd class="text-gray-900">{{ formatDate(image.taken_at) }}</dd>
                </div>
                <div v-if="image.license" class="flex justify-between">
                  <dt class="text-gray-500">License</dt>
                  <dd class="text-gray-900">{{ image.license }}</dd>
                </div>
              </dl>
            </div>

            <!-- Available Versions -->
            <div v-if="image.versions && image.versions.length > 1" class="mt-6 bg-white rounded-lg shadow-sm p-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Downloads</h3>
              <div class="space-y-2">
                <div
                  v-for="version in image.versions"
                  :key="`${version.variant}-${version.format}`"
                  class="flex items-center justify-between py-2"
                >
                  <div>
                    <div class="text-sm font-medium text-gray-900">
                      {{ formatVariantName(version.variant) }} ({{ version.format.toUpperCase() }})
                    </div>
                    <div class="text-xs text-gray-500">
                      {{ version.width }}×{{ version.height }} • {{ formatBytes(version.size_bytes) }}
                    </div>
                  </div>
                  <button
                    @click="downloadVersion(version)"
                    class="text-blue-600 hover:text-blue-500 text-sm font-medium"
                  >
                    Download
                  </button>
                </div>
              </div>
            </div>

            <!-- Related Images -->
            <div v-if="relatedImages.length > 0" class="mt-6 bg-white rounded-lg shadow-sm p-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Related Images</h3>
              <div class="grid grid-cols-2 gap-3">
                <Link
                  v-for="relatedImage in relatedImages"
                  :key="relatedImage.id"
                  :href="route('images.show', relatedImage.slug)"
                  class="aspect-square bg-gray-100 rounded-lg overflow-hidden hover:shadow-md transition-shadow"
                >
                  <img
                    :src="getRelatedImageUrl(relatedImage)"
                    :alt="relatedImage.alt_text || relatedImage.title"
                    class="w-full h-full object-cover hover:scale-105 transition-transform"
                  >
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Lightbox -->
      <Lightbox
        v-if="lightboxOpen"
        :images="[image]"
        :initial-index="0"
        @close="lightboxOpen = false"
      />
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import route from 'ziggy-js'
import {
  ChevronRightIcon,
  ChevronDownIcon,
  HeartIcon,
  ShareIcon,
  EllipsisVerticalIcon,
  MagnifyingGlassIcon,
} from '@heroicons/vue/24/outline'

import AppLayout from '@/Layouts/AppLayout.vue'
import UserAvatar from '@/Components/UserAvatar.vue'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import Lightbox from '@/Components/Gallery/Lightbox.vue'
import CommentsSection from '@/Components/CommentsSection.vue'

const page = usePage()

const props = defineProps({
  image: Object,
  relatedImages: {
    type: Array,
    default: () => [],
  },
  comments: {
    type: Array,
    default: () => [],
  },
  userLike: {
    type: Boolean,
    default: false,
  },
  can: {
    type: Object,
    default: () => ({}),
  },
})

const lightboxOpen = ref(false)
const isLiked = ref(props.userLike)

const formatNumber = (number) => {
  if (number >= 1000000) {
    return (number / 1000000).toFixed(1) + 'M'
  } else if (number >= 1000) {
    return (number / 1000).toFixed(1) + 'K'
  }
  return number.toString()
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}

const formatBytes = (bytes) => {
  if (bytes === 0) return '0 B'
  const k = 1024
  const sizes = ['B', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const formatVariantName = (variant) => {
  const names = {
    thumb: 'Thumbnail',
    small: 'Small',
    medium: 'Medium',
    large: 'Large',
    original: 'Original',
  }
  return names[variant] || variant
}

const getImageUrl = (variant = 'large') => {
  if (props.image.versions) {
    const version = props.image.versions.find(v => v.variant === variant)
    if (version) return version.url
  }
  return props.image.url || props.image.storage_path
}

const getRelatedImageUrl = (image) => {
  if (image.versions) {
    const version = image.versions.find(v => v.variant === 'small')
    if (version) return version.url
  }
  return image.url || image.storage_path
}

const openLightbox = () => {
  lightboxOpen.value = true
}

const toggleLike = async () => {
  try {
    await router.post(route('images.like', props.image.slug), {}, {
      preserveState: true,
      preserveScroll: true,
    })
    isLiked.value = !isLiked.value
  } catch (error) {
    console.error('Failed to toggle like:', error)
  }
}

const copyLink = () => {
  const url = window.location.href
  navigator.clipboard.writeText(url).then(() => {
    // Show success notification
  })
}

const shareImage = () => {
  if (navigator.share) {
    navigator.share({
      title: props.image.title,
      text: props.image.caption,
      url: window.location.href,
    })
  } else {
    copyLink()
  }
}

const downloadImage = () => {
  window.open(route('images.download', props.image.slug), '_blank')
}

const downloadVersion = (version) => {
  window.open(route('images.download', [props.image.slug, { variant: version.variant }]), '_blank')
}

const togglePublish = () => {
  router.post(route('images.toggle-publish', props.image.slug))
}

const deleteImage = () => {
  if (confirm('Are you sure you want to delete this image? This action cannot be undone.')) {
    router.delete(route('images.destroy', props.image.slug))
  }
}
</script>
