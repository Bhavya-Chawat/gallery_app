<template>
  <AppLayout>
    <div class="min-h-screen bg-gray-50 dark:bg-slate-900">
      <!-- Breadcrumbs -->
      <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl border-b border-gray-200 dark:border-slate-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
          <nav class="flex" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-4">
              <li>
                <Link href="/gallery" class="text-gray-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-cyan-400">
                  Gallery
                </Link>
              </li>
              <li class="flex items-center">
                <svg class="flex-shrink-0 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
                <span class="ml-4 text-sm font-medium text-gray-900 dark:text-gray-100">
                  {{ image.title || 'Untitled' }}
                </span>
              </li>
            </ol>
          </nav>
        </div>
      </div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Main Image Area -->
          <div class="lg:col-span-2">
            <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl rounded-2xl shadow-xl overflow-hidden">
              <!-- Image Container -->
              <div class="relative aspect-auto bg-gray-100 dark:bg-slate-700">
                <img 
                  :src="image.full_url || image.url" 
                  :alt="image.alt_text || image.title"
                  class="w-full h-auto max-h-[70vh] object-contain cursor-zoom-in"
                  @click="openLightbox"
                  @load="imageLoaded = true"
                />
                <div v-if="!imageLoaded" class="absolute inset-0 flex items-center justify-center">
                  <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                </div>
              </div>

              <!-- Image Actions -->
              <div class="p-6 border-t border-gray-200 dark:border-slate-700">
                <div class="flex items-center justify-between">
                  <div class="flex items-center space-x-4">
                    <!-- Like Button -->
                    <button 
                      @click="toggleLike"
                      :class="[
                        'flex items-center space-x-2 px-4 py-2 rounded-lg transition-all duration-200',
                        image.is_liked 
                          ? 'bg-gradient-to-r from-pink-500 to-red-500 text-white shadow-lg' 
                          : 'bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-pink-500 hover:to-red-500 hover:text-white'
                      ]"
                      :disabled="likingInProgress"
                    >
                      <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                      </svg>
                      <span>{{ image.likes_count }} {{ image.likes_count === 1 ? 'Like' : 'Likes' }}</span>
                    </button>

                    <!-- Share Button -->
                    <button 
                      @click="shareImage"
                      class="flex items-center space-x-2 px-4 py-2 rounded-lg bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-blue-600 hover:to-cyan-500 hover:text-white transition-all duration-200"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z" />
                      </svg>
                      <span>Share</span>
                    </button>

                    <!-- Download Button -->
                    <button 
                      v-if="canDownload"
                      @click="downloadImage"
                      class="flex items-center space-x-2 px-4 py-2 rounded-lg bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-green-500 hover:to-emerald-500 hover:text-white transition-all duration-200"
                      :disabled="downloadingInProgress"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                      </svg>
                      <span>{{ downloadingInProgress ? 'Downloading...' : 'Download' }}</span>
                    </button>
                  </div>

                  <!-- View Count -->
                  <div class="text-sm text-gray-500 dark:text-slate-400">
                    {{ image.views_count }} {{ image.views_count === 1 ? 'view' : 'views' }}
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Sidebar -->
          <div class="space-y-6">
            <!-- Image Metadata Editor -->
            <ImageMetadataEditor 
              :image="image"
              :editable="canEdit"
              @save="updateImageMetadata"
            />

            <!-- Comments Section -->
            <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl rounded-2xl shadow-xl">
              <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                  Comments ({{ comments.length }})
                </h3>
                
                <!-- Add Comment Form -->
                <CommentsForm 
                  v-if="$page.props.auth.user"
                  :image-id="image.id"
                  @comment-added="addComment"
                  class="mb-6"
                />

                <!-- Login prompt for guests -->
                <div v-else class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                  <p class="text-blue-800 dark:text-blue-300 text-sm">
                    <Link href="/login" class="font-medium hover:underline">Log in</Link> 
                    to leave a comment
                  </p>
                </div>

                <!-- Comments List -->
                <CommentsList 
                  :comments="comments"
                  :can-moderate="canModerate"
                  @comment-updated="updateComment"
                  @comment-deleted="deleteComment"
                  @comment-liked="toggleCommentLike"
                />
              </div>
            </div>

            <!-- Related Images -->
            <div v-if="relatedImages.length > 0" class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl rounded-2xl shadow-xl">
              <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Related Images</h3>
                <div class="grid grid-cols-2 gap-4">
                  <Link 
                    v-for="relatedImage in relatedImages" 
                    :key="relatedImage.id"
                    :href="`/gallery/images/${relatedImage.id}`"
                    class="group relative aspect-square overflow-hidden rounded-lg bg-gray-100 dark:bg-slate-700"
                  >
                    <img 
                      :src="relatedImage.thumb_url" 
                      :alt="relatedImage.title"
                      class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200"
                    />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                      <div class="absolute bottom-2 left-2 text-white text-sm font-medium">
                        {{ relatedImage.title }}
                      </div>
                    </div>
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Lightbox Modal -->
      <Lightbox 
        v-if="lightboxOpen"
        :images="[image]"
        :initial-index="0"
        :keyboard-enabled="true"
        @close="closeLightbox"
      />
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import CommentsForm from '@/Components/Gallery/CommentsForm.vue'
import Lightbox from '@/Components/Gallery/Lightbox.vue'
import CommentsList from '@/Components/Gallery/CommentsList.vue'
import axios from 'axios'

// Props from backend
const props = defineProps({
  image: Object,
  relatedImages: Array,
  canEdit: Boolean,
  canDownload: Boolean,
  canModerate: Boolean
})

// Reactive data
const imageLoaded = ref(false)
const lightboxOpen = ref(false)
const likingInProgress = ref(false)
const downloadingInProgress = ref(false)
const comments = ref([])

// Computed
const page = usePage()

// Methods
const toggleLike = async () => {
  if (likingInProgress.value || !page.props.auth.user) return
  
  likingInProgress.value = true
  try {
    const response = await axios.post(`/api/v1/images/${props.image.id}/like`)
    props.image.is_liked = response.data.liked
    props.image.likes_count = response.data.likes_count
  } catch (error) {
    console.error('Error toggling like:', error)
  } finally {
    likingInProgress.value = false
  }
}

const shareImage = async () => {
  if (navigator.share) {
    try {
      await navigator.share({
        title: props.image.title || 'Beautiful Image',
        url: window.location.href
      })
    } catch (error) {
      // Fallback to clipboard
      copyToClipboard()
    }
  } else {
    copyToClipboard()
  }
}

const copyToClipboard = async () => {
  try {
    await navigator.clipboard.writeText(window.location.href)
    // You might want to show a toast notification here
  } catch (error) {
    console.error('Failed to copy URL:', error)
  }
}

const downloadImage = async () => {
  if (downloadingInProgress.value) return
  
  downloadingInProgress.value = true
  try {
    const response = await axios.get(`/api/v1/images/${props.image.id}/signed-url`)
    const link = document.createElement('a')
    link.href = response.data.download_url
    link.download = props.image.filename || 'image.jpg'
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
  } catch (error) {
    console.error('Error downloading image:', error)
  } finally {
    downloadingInProgress.value = false
  }
}

const openLightbox = () => {
  lightboxOpen.value = true
}

const closeLightbox = () => {
  lightboxOpen.value = false
}

const updateImageMetadata = async (metadata) => {
  try {
    await axios.put(`/api/v1/images/${props.image.id}`, metadata)
    Object.assign(props.image, metadata)
  } catch (error) {
    console.error('Error updating image metadata:', error)
  }
}

const loadComments = async () => {
  try {
    const response = await axios.get(`/api/v1/images/${props.image.id}/comments`)
    comments.value = response.data.data
  } catch (error) {
    console.error('Error loading comments:', error)
  }
}

const addComment = (comment) => {
  comments.value.unshift(comment)
}

const updateComment = (commentId, updatedComment) => {
  const index = comments.value.findIndex(c => c.id === commentId)
  if (index !== -1) {
    comments.value[index] = updatedComment
  }
}

const deleteComment = (commentId) => {
  comments.value = comments.value.filter(c => c.id !== commentId)
}

const toggleCommentLike = async (commentId) => {
  try {
    const response = await axios.post(`/api/v1/comments/${commentId}/like`)
    const comment = comments.value.find(c => c.id === commentId)
    if (comment) {
      comment.is_liked = response.data.liked
      comment.likes_count = response.data.likes_count
    }
  } catch (error) {
    console.error('Error toggling comment like:', error)
  }
}

// Lifecycle
onMounted(() => {
  loadComments()
})
</script>