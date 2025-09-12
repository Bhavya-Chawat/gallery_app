<template>
  <AppLayout>
    <Head :title="image.title || 'Image'" />

    <template #header>
      <!-- Breadcrumb Navigation -->
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
            <span class="ml-4 text-sm font-medium text-gray-900">
              {{ image.title || 'Untitled' }}
            </span>
          </li>
        </ol>
      </nav>
    </template>

    <div class="py-8">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="lg:grid lg:grid-cols-3 lg:gap-8">
          <!-- Main Image Display -->
          <div class="lg:col-span-2">
            <!-- Image Container -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
              <div class="relative">
                <img
                  :src="getImageUrl()"
                  :alt="image.alt_text || image.title"
                  class="w-full h-auto max-h-screen object-contain"
                  @error="handleImageError"
                >
                
                <!-- Privacy Badge -->
                <div class="absolute top-4 left-4">
                  <span
                    v-if="image.privacy === 'private'"
                    class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-red-100 text-red-700"
                  >Private</span>
                  <span
                    v-else-if="image.privacy === 'unlisted'"
                    class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-yellow-100 text-yellow-800"
                  >Unlisted</span>
                  <span
                    v-else
                    class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-green-100 text-green-800"
                  >Public</span>
                </div>
              </div>
            </div>

            <!-- Image Information -->
            <div class="mt-6 bg-white rounded-lg shadow-sm p-6">
              <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                  <h1 class="text-3xl font-bold text-gray-900">
                    {{ image.title || 'Untitled' }}
                  </h1>
                  <p v-if="image.caption" class="mt-3 text-lg text-gray-600">
                    {{ image.caption }}
                  </p>
                  
                  <!-- Owner Info -->
                  <div class="mt-4 flex items-center">
                    <UserAvatar :user="image.owner" size="md" />
                    <div class="ml-3">
                      <p class="text-sm font-medium text-gray-900">{{ image.owner.name }}</p>
                      <p class="text-xs text-gray-500">{{ formatDate(image.created_at) }}</p>
                    </div>
                  </div>
                </div>

                <!-- Action Buttons -->
                <div class="ml-6 flex items-center space-x-3">
                  <!-- Like Button -->
                  <button
                    @click="toggleLike"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                    :class="{ 'text-red-600 border-red-300 bg-red-50': isLiked }"
                  >
                    <HeartIcon class="h-4 w-4 mr-2" :class="{ 'fill-current': isLiked }" />
                    {{ currentLikeCount }}
                  </button>

                  <!-- Share Button -->
                  <Dropdown align="right" width="48">
                    <template #trigger>
                      <button class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <ShareIcon class="h-4 w-4 mr-2" />
                        Share
                        <ChevronDownIcon class="ml-2 h-4 w-4" />
                      </button>
                    </template>
<template #content>
  <DropdownLink @click="copyLink">Copy Link</DropdownLink>
  <DropdownLink @click="shareImage">Share via...</DropdownLink>
  <div class="border-t border-gray-100" v-if="can.download"></div>
  <DropdownLink @click="downloadImage" v-if="can.download">Download Original</DropdownLink>
  <DropdownLink @click="downloadWithWatermark" v-if="can.download">Download with Watermark</DropdownLink>
</template>

                  </Dropdown>

                  <!-- Edit/Admin Actions -->
                  <Dropdown align="right" width="48" v-if="can.update || can.delete">
                    <template #trigger>
                      <button class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
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

              <!-- Tags -->
              <div v-if="image.tags && image.tags.length" class="mt-6">
                <h3 class="text-sm font-medium text-gray-900 mb-2">Tags</h3>
                <div class="flex flex-wrap gap-2">
                  <Link
                    v-for="tag in image.tags"
                    :key="tag.id"
<!--                     :href="route('tags.show', tag.slug)" -->
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 hover:bg-blue-200"
                  >
                    {{ tag.name }}
                  </Link>
                </div>
              </div>

              <!-- Stats -->
              <div class="mt-6 grid grid-cols-3 gap-6 text-center">
                <div>
                  <div class="text-2xl font-bold text-gray-900">{{ formatNumber(image.views_count) }}</div>
                  <div class="text-sm text-gray-500">Views</div>
                </div>
                <div>
                  <div class="text-2xl font-bold text-gray-900">{{ formatNumber(currentLikeCount) }}</div>
                  <div class="text-sm text-gray-500">Likes</div>
                </div>
                <div>
                  <div class="text-2xl font-bold text-gray-900">{{ formatNumber(currentCommentCount) }}</div>
                  <div class="text-sm text-gray-500">Comments</div>
                </div>
              </div>
            </div>

            <!-- Comments Section -->
            <div v-if="image.allow_comments" class="mt-6 bg-white rounded-lg shadow-sm p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-6">
                Comments ({{ currentCommentCount }})
              </h3>
              
              <!-- Add Comment Form -->
              <div v-if="can.comment" class="mb-6">
                <form @submit.prevent="submitComment" class="space-y-4">
                  <textarea
                    v-model="newComment"
                    rows="3"
                    class="w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    placeholder="Write a comment..."
                    required
                  ></textarea>
                  <button
                    type="submit"
                    :disabled="submittingComment || !newComment.trim()"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50"
                  >
                    {{ submittingComment ? 'Posting...' : 'Post Comment' }}
                  </button>
                </form>
              </div>

              <!-- Comments List -->
              <div class="space-y-4">
                <div
                  v-for="comment in currentComments"
                  :key="comment.id"
                  class="border-l-4 border-gray-200 pl-4"
                >
                  <div class="flex items-start space-x-3">
                    <UserAvatar :user="comment.user" size="sm" />
                    <div class="flex-1 min-w-0">
                      <div class="flex items-center space-x-2">
                        <span class="text-sm font-medium text-gray-900">{{ comment.user.name }}</span>
                        <span class="text-xs text-gray-500">{{ formatDate(comment.created_at) }}</span>
                      </div>
                      <p class="mt-1 text-sm text-gray-700">{{ comment.body }}</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- No Comments State -->
              <div v-if="currentComments.length === 0" class="text-center py-6">
                <p class="text-gray-500">No comments yet. Be the first to comment!</p>
              </div>
            </div>
          </div>

          <!-- Sidebar -->
          <div class="mt-8 lg:mt-0 space-y-6">
            <!-- Image Details -->
            <div class="bg-white rounded-lg shadow-sm p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Details</h3>
              <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                  <dt class="text-gray-500">Uploaded</dt>
                  <dd class="text-gray-900 font-medium">{{ formatDate(image.created_at) }}</dd>
                </div>
                <div class="flex justify-between">
                  <dt class="text-gray-500">Dimensions</dt>
                  <dd class="text-gray-900">{{ image.dimensions || 'N/A' }}</dd>
                </div>
                <div class="flex justify-between">
                  <dt class="text-gray-500">File Size</dt>
                  <dd class="text-gray-900">{{ image.formatted_size || 'N/A' }}</dd>
                </div>
                <div class="flex justify-between">
                  <dt class="text-gray-500">Privacy</dt>
                  <dd class="text-gray-900 capitalize">{{ image.privacy }}</dd>
                </div>
                <div v-if="image.album" class="flex justify-between">
                  <dt class="text-gray-500">Album</dt>
                  <dd class="text-gray-900">
                    <Link :href="route('albums.show', image.album.slug)" class="text-blue-600 hover:text-blue-500">
                      {{ image.album.title }}
                    </Link>
                  </dd>
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

            <!-- Download Options -->
            <div v-if="can.download && image.versions && image.versions.length > 0" class="bg-white rounded-lg shadow-sm p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Downloads</h3>
              <div class="space-y-3">
                <div
                  v-for="version in image.versions"
                  :key="`${version.variant}-${version.format}`"
                  class="flex items-center justify-between py-2 border-b border-gray-100 last:border-b-0"
                >
                  <div>
                    <div class="text-sm font-medium text-gray-900">
                      {{ formatVariantName(version.variant) }}
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
            <div v-if="relatedImages.length > 0" class="bg-white rounded-lg shadow-sm p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Related Images</h3>
              <div class="grid grid-cols-2 gap-3">
                <Link
                  v-for="relatedImage in relatedImages"
                  :key="relatedImage.id"
                  :href="route('images.show', relatedImage.slug)"
                  class="aspect-square bg-gray-100 rounded-lg overflow-hidden hover:shadow-md transition-shadow group"
                >
                  <img
                    :src="getRelatedImageUrl(relatedImage)"
                    :alt="relatedImage.alt_text || relatedImage.title"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform"
                  >
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import route from 'ziggy-js'
import {
  ChevronRightIcon,
  ChevronDownIcon,
  HeartIcon,
  ShareIcon,
  EllipsisVerticalIcon,
} from '@heroicons/vue/24/outline'

import AppLayout from '@/Layouts/AppLayout.vue'
import UserAvatar from '@/Components/UserAvatar.vue'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'

const props = defineProps({
  image: { type: Object, default: () => ({}) },
  relatedImages: { type: Array, default: () => [] },
  comments: { type: Array, default: () => [] },
  userLike: { type: Boolean, default: false },
  can: { type: Object, default: () => ({}) },
  auth: { type: Object, default: () => ({ user: null, roles: [] }) },
  errors: { type: Object, default: () => ({}) },
})

const isLiked = ref(props.userLike)
const imageError = ref(false)
const newComment = ref('')
const submittingComment = ref(false)
const currentComments = ref([...props.comments])

// Reactive counters
const currentLikeCount = ref(props.image.likes_count)
const currentCommentCount = computed(() => currentComments.value.length)

const formatNumber = (number) => {
  if (number >= 1000000) {
    return (number / 1000000).toFixed(1) + 'M'
  } else if (number >= 1000) {
    return (number / 1000).toFixed(1) + 'K'
  }
  return number?.toString() || '0'
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

const getImageUrl = () => {
  if (imageError.value) {
    return '/images/placeholder.jpg'
  }
  
  if (props.image.storage_path) {
    return `http://localhost:9000/gallery-images/${props.image.storage_path}`
  }
  
  return props.image.url || '/images/placeholder.jpg'
}

const getRelatedImageUrl = (image) => {
  if (image.storage_path) {
    return `http://localhost:9000/gallery-images/${image.storage_path}`
  }
  return image.url || '/images/placeholder.jpg'
}

const handleImageError = () => {
  imageError.value = true
}

const toggleLike = async () => {
  if (!props.auth.user) return
  
  try {
    await router.post(route('images.like', props.image.slug), {}, {
      preserveState: true,
      preserveScroll: true,
    })
    isLiked.value = !isLiked.value
    currentLikeCount.value += isLiked.value ? 1 : -1
  } catch (error) {
    console.error('Failed to toggle like:', error)
  }
}

const submitComment = async () => {
  if (!newComment.value.trim()) return
  
  submittingComment.value = true
  try {
    const response = await router.post(`/images/${props.image.slug}/comments`, {
      body: newComment.value
    }, {
      preserveState: true,
      preserveScroll: true,
    })
    
    // Add comment locally for immediate feedback
    currentComments.value.unshift({
      id: Date.now(),
      body: newComment.value,
      user: props.auth.user,
      created_at: new Date().toISOString()
    })
    
    newComment.value = ''
  } catch (error) {
    console.error('Failed to post comment:', error)
  } finally {
    submittingComment.value = false
  }
}

const copyLink = () => {
  const url = window.location.href
  navigator.clipboard.writeText(url).then(() => {
    // TODO: Show success notification
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
  // Create a hidden link to force download
  const link = document.createElement('a')
  link.href = route('images.download', props.image.slug)
  link.download = props.image.original_filename || 'image'
  link.style.display = 'none'
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
}

const downloadVersion = (version) => {
  const link = document.createElement('a')
  link.href = route('images.download', [props.image.slug, { variant: version.variant }])
  link.download = `${props.image.original_filename}_${version.variant}` || 'image'
  link.style.display = 'none'
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
}

// Add watermark download option
const downloadWithWatermark = () => {
  const link = document.createElement('a')
  link.href = route('images.download', [props.image.slug, { watermark: 1 }])
  link.download = `watermarked_${props.image.original_filename}` || 'image'
  link.style.display = 'none'
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
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
