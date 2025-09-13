<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      
      <!-- Back Button -->
      <div class="mb-6">
        <a href="/collections" class="text-blue-600 hover:text-blue-800">
          ← Back to Collections
        </a>
      </div>

      <!-- Collection Header -->
      <div class="bg-white rounded-lg shadow p-6 mb-8">
        <div class="flex justify-between items-start">
          <div class="flex-1">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
              {{ collection?.title || 'Collection' }}
            </h1>
            <p v-if="collection?.description" class="text-gray-600 mb-4">
              {{ collection.description }}
            </p>
            
            <div class="flex items-center space-x-6 text-sm text-gray-500">
              <span>by {{ collection?.curator?.name || 'Unknown' }}</span>
              <span>{{ collection?.images_count || 0 }} items</span>
              <span>{{ formatDate(collection?.created_at) }}</span>
            </div>
          </div>
          
          <div v-if="canEdit" class="ml-4 flex space-x-2">
            <button
              @click="openAddModal"
              class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
            >
              Add Images
            </button>
            
            <a
              :href="`/collections/${collection?.slug}/edit`"
              class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700"
            >
              Edit Collection
            </a>
          </div>
        </div>
      </div>

      <!-- Collection Items Grid -->
      <div v-if="hasImages" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-8">
        <div
          v-for="image in imagesList"
          :key="image.id"
          class="group relative aspect-square bg-gray-200 rounded-lg overflow-hidden"
        >
          <a :href="`/images/${image.slug}`">
            <img
              :src="getImageUrl(image)"
              :alt="image.alt_text || image.title"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200"
              loading="lazy"
              @error="handleImageError"
            />
          </a>
          
          <!-- Remove Button -->
          <button
            v-if="canEdit"
            @click="removeFromCollection(image.id)"
            class="absolute top-2 right-2 p-1 bg-red-500 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity"
            title="Remove from collection"
          >
            ✕
          </button>

          <!-- Image Info -->
          <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-3">
            <h3 class="text-white text-sm font-medium truncate">{{ image.title }}</h3>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-12 bg-white rounded-lg">
        <div class="text-gray-400 text-6xl mb-4">📁</div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No images in this collection</h3>
        <p class="text-gray-500 mb-6">
          <span v-if="canEdit">Get started by adding some images.</span>
          <span v-else>This collection doesn't have any images yet.</span>
        </p>
        <button
          v-if="canEdit"
          @click="openAddModal"
          class="px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700"
        >
          Add Your First Image
        </button>
      </div>

      <!-- Simple Pagination -->
      <div v-if="showPagination" class="flex justify-center space-x-2 mt-8">
        <a
          v-for="link in paginationLinks"
          :key="link.label"
          :href="link.url || '#'"
          :class="[
            'px-3 py-2 text-sm rounded-md',
            link.active 
              ? 'bg-blue-500 text-white' 
              : 'bg-white text-gray-700 hover:bg-gray-50 border',
            !link.url ? 'opacity-50 cursor-not-allowed' : ''
          ]"
          v-html="link.label"
        />
      </div>
    </div>

    <!-- Working Add Images Modal -->
    <div v-if="showAddModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click="closeModal">
      <div class="bg-white rounded-lg p-6 max-w-4xl w-full mx-4 max-h-[80vh] overflow-y-auto" @click.stop>
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-medium text-gray-900">Add Images to Collection</h3>
          <button
            @click="closeModal"
            class="text-gray-400 hover:text-gray-600 text-2xl"
          >
            ✕
          </button>
        </div>

        <!-- Search Images -->
        <div class="mb-4">
          <input
            v-model="searchQuery"
            @input="searchImages"
            type="text"
            placeholder="Search your images..."
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
          />
        </div>

        <!-- Loading State -->
        <div v-if="loadingImages" class="text-center py-8">
          <div class="text-gray-500">Loading images...</div>
        </div>

        <!-- Available Images Grid -->
        <div v-else-if="availableImages.length > 0" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-6 max-h-96 overflow-y-auto">
          <div
            v-for="image in availableImages"
            :key="image.id"
            @click="toggleImageSelection(image)"
            :class="[
              'relative aspect-square bg-gray-200 rounded-lg overflow-hidden cursor-pointer border-2 transition-all',
              selectedImages.includes(image.id) 
                ? 'border-blue-500 ring-2 ring-blue-200' 
                : 'border-gray-200 hover:border-gray-300'
            ]"
          >
            <img
              :src="getImageUrl(image)"
              :alt="image.title"
              class="w-full h-full object-cover"
              loading="lazy"
              @error="handleImageError"
            />
            
            <!-- Selection indicator -->
            <div
              v-if="selectedImages.includes(image.id)"
              class="absolute top-2 right-2 bg-blue-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm"
            >
              ✓
            </div>
            
            <!-- Image title -->
            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-2">
              <p class="text-white text-xs truncate">{{ image.title }}</p>
            </div>
          </div>
        </div>

        <!-- No images found -->
        <div v-else class="text-center py-8">
          <div class="text-gray-400 text-4xl mb-4">🖼️</div>
          <p class="text-gray-500">No images found. Try adjusting your search or upload some images first.</p>
        </div>

        <!-- Modal Actions -->
        <div class="flex justify-between items-center pt-4 border-t">
          <p class="text-sm text-gray-600">
            {{ selectedImages.length }} image(s) selected
          </p>
          
          <div class="flex space-x-3">
            <button
              @click="closeModal"
              class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
            >
              Cancel
            </button>
            <button
              @click="addSelectedImages"
              :disabled="selectedImages.length === 0 || addingImages"
              :class="[
                'px-4 py-2 text-white rounded-md',
                selectedImages.length > 0 && !addingImages
                  ? 'bg-blue-600 hover:bg-blue-700'
                  : 'bg-gray-400 cursor-not-allowed'
              ]"
            >
              {{ addingImages ? 'Adding...' : `Add ${selectedImages.length} Image(s)` }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  collection: Object,
  images: Object,
  can: Object,
})

// Reactive state
const showAddModal = ref(false)
const availableImages = ref([])
const selectedImages = ref([])
const searchQuery = ref('')
const loadingImages = ref(false)
const addingImages = ref(false)

// Computed properties
const canEdit = computed(() => props.can?.update || false)

const hasImages = computed(() => {
  return props.images?.data && props.images.data.length > 0
})

const imagesList = computed(() => {
  return props.images?.data || []
})

const showPagination = computed(() => {
  return props.images?.links && props.images.links.length > 3
})

const paginationLinks = computed(() => {
  return props.images?.links || []
})

// Methods
const openAddModal = async () => {
  showAddModal.value = true
  selectedImages.value = []
  searchQuery.value = ''
  await loadAvailableImages()
}

const closeModal = () => {
  showAddModal.value = false
  selectedImages.value = []
  availableImages.value = []
  searchQuery.value = ''
}

const loadAvailableImages = async () => {
  loadingImages.value = true
  try {
    const response = await fetch('/api/my-images?limit=50')
    const data = await response.json()
    availableImages.value = data.images || []
  } catch (error) {
    console.error('Failed to load images:', error)
    availableImages.value = []
    alert('Failed to load images. Please try again.')
  } finally {
    loadingImages.value = false
  }
}

const searchImages = async () => {
  if (!searchQuery.value.trim()) {
    await loadAvailableImages()
    return
  }

  loadingImages.value = true
  try {
    const response = await fetch(`/api/search-images?q=${encodeURIComponent(searchQuery.value)}`)
    const data = await response.json()
    availableImages.value = data.images || []
  } catch (error) {
    console.error('Failed to search images:', error)
    availableImages.value = []
  } finally {
    loadingImages.value = false
  }
}

const toggleImageSelection = (image) => {
  const index = selectedImages.value.indexOf(image.id)
  if (index > -1) {
    selectedImages.value.splice(index, 1)
  } else {
    selectedImages.value.push(image.id)
  }
}

const addSelectedImages = async () => {
  if (selectedImages.value.length === 0) return

  addingImages.value = true
  try {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    
    // Add each selected image to the collection
    const promises = selectedImages.value.map(imageId => 
      fetch(`/collections/${props.collection.slug}/add-image`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': token || ''
        },
        body: JSON.stringify({
          image_id: imageId
        })
      })
    )

    await Promise.all(promises)
    
    // Close modal and reload page
    closeModal()
    window.location.reload()
    
  } catch (error) {
    console.error('Failed to add images:', error)
    alert('Failed to add images to collection. Please try again.')
  } finally {
    addingImages.value = false
  }
}

const removeFromCollection = (imageId) => {
  if (confirm('Are you sure you want to remove this image from the collection?')) {
    // Simple form submission
    const form = document.createElement('form')
    form.method = 'POST'
    form.action = `/collections/${props.collection.slug}/remove-image`
    
    const methodInput = document.createElement('input')
    methodInput.type = 'hidden'
    methodInput.name = '_method'
    methodInput.value = 'DELETE'
    form.appendChild(methodInput)
    
    const tokenInput = document.createElement('input')
    tokenInput.type = 'hidden'
    tokenInput.name = '_token'
    tokenInput.value = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
    form.appendChild(tokenInput)
    
    const imageInput = document.createElement('input')
    imageInput.type = 'hidden'
    imageInput.name = 'image_id'
    imageInput.value = imageId
    form.appendChild(imageInput)
    
    document.body.appendChild(form)
    form.submit()
  }
}

const getImageUrl = (image) => {
  // Try different image URL patterns
  if (image?.thumbnail_url) {
    return image.thumbnail_url
  }
  
  if (image?.storage_path) {
    // Try MinIO URL first (based on your previous setup)
    return `http://localhost:9000/gallery-images/${image.storage_path}`
  }
  
  // Fallback to Laravel storage
  if (image?.storage_path) {
    return `/storage/${image.storage_path}`
  }
  
  // Ultimate fallback
  return '/images/placeholder.jpg'
}

const handleImageError = (event) => {
  // Try fallback URLs when image fails to load
  const img = event.target
  const currentSrc = img.src
  
  if (currentSrc.includes('localhost:9000')) {
    // Try Laravel storage path
    const imagePath = currentSrc.split('/').pop()
    img.src = `/storage/${imagePath}`
  } else if (currentSrc.includes('/storage/')) {
    // Use placeholder
    img.src = '/images/placeholder.jpg'
  }
}

const formatDate = (date) => {
  if (!date) return 'Unknown date'
  
  try {
    return new Date(date).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    })
  } catch (e) {
    return 'Invalid date'
  }
}
</script>
/*<template>
  <div class="min-h-screen bg-slate-900 relative overflow-hidden">
    <!-- Animated Mesh Background -->
    <div class="absolute inset-0 opacity-30">
      <svg class="absolute inset-0 w-full h-full" xmlns="http://www.w3.org/2000/svg">
        <defs>
          <pattern id="mesh" x="0" y="0" width="100" height="100" patternUnits="userSpaceOnUse">
            <path d="M0 0h100v100H0z" fill="none"/>
            <path d="M0 0h100M0 50h100M0 100h100" stroke="url(#meshGradient)" stroke-width="0.5" opacity="0.4"/>
            <path d="M0 0v100M50 0v100M100 0v100" stroke="url(#meshGradient)" stroke-width="0.5" opacity="0.4"/>
          </pattern>
          <linearGradient id="meshGradient" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#8b5cf6" class="animate-pulse"/>
            <stop offset="50%" stop-color="#06b6d4" class="animate-pulse"/>
            <stop offset="100%" stop-color="#a855f7" class="animate-pulse"/>
          </linearGradient>
        </defs>
        <rect width="100%" height="100%" fill="url(#mesh)" class="animate-pulse"/>
      </svg>
    </div>

    <!-- Floating Particles -->
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
      <div v-for="i in 20" :key="i" 
           :class="`absolute w-2 h-2 bg-gradient-to-r from-violet-400 to-cyan-400 rounded-full opacity-60 animate-bounce`"
           :style="{
             left: Math.random() * 100 + '%',
             top: Math.random() * 100 + '%',
             animationDelay: Math.random() * 8 + 's',
             animationDuration: (3 + Math.random() * 4) + 's'
           }">
      </div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      
      <!-- Back Button -->
      <div class="mb-6 animate-fade-in-up">
        <a href="/collections" 
           class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-xl border border-white/20 text-slate-300 hover:text-white rounded-xl transition-all duration-300 hover:bg-white/20 hover:scale-105 hover:shadow-lg hover:shadow-violet-500/25">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
          </svg>
          Back to Collections
        </a>
      </div>

      <!-- Collection Header -->
      <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl shadow-2xl shadow-violet-500/20 p-8 mb-8 animate-fade-in-up" style="animation-delay: 0.1s">
        <div class="flex justify-between items-start">
          <div class="flex-1">
            <h1 class="text-4xl md:text-5xl font-bold mb-4 bg-gradient-to-r from-violet-400 via-purple-400 to-cyan-400 bg-clip-text text-transparent animate-shimmer bg-[length:200%_100%]">
              {{ collection?.title || 'Collection' }}
            </h1>
            <p v-if="collection?.description" class="text-slate-300 text-lg mb-6 leading-relaxed">
              {{ collection.description }}
            </p>
            
            <div class="flex flex-wrap items-center gap-6 text-sm text-slate-400">
              <div class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-gradient-to-r from-violet-500 to-purple-500 rounded-full flex items-center justify-center">
                  <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                  </svg>
                </div>
                <span class="text-slate-300">{{ collection?.curator?.name || 'Unknown' }}</span>
              </div>
              
              <div class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-gradient-to-r from-cyan-500 to-blue-500 rounded-full flex items-center justify-center">
                  <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                  </svg>
                </div>
                <span class="text-slate-300">{{ collection?.images_count || 0 }} items</span>
              </div>
              
              <div class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center">
                  <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a1 1 0 011-1h6a1 1 0 011 1v4m-5 8h.01M8 21h8a2 2 0 002-2V7a2 2 0 00-2-2H8a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                  </svg>
                </div>
                <span class="text-slate-300">{{ formatDate(collection?.created_at) }}</span>
              </div>
            </div>
          </div>
          
          <div v-if="canEdit" class="ml-6 flex flex-col sm:flex-row gap-3">
            <button
              @click="openAddModal"
              class="px-6 py-3 bg-gradient-to-r from-violet-600 to-purple-600 text-white rounded-xl font-medium transition-all duration-300 hover:from-violet-700 hover:to-purple-700 hover:scale-105 hover:shadow-lg hover:shadow-violet-500/50 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-offset-2 focus:ring-offset-slate-900"
            >
              <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
              </svg>
              Add Images
            </button>
            
            <a
              :href="`/collections/${collection?.slug}/edit`"
              class="px-6 py-3 bg-white/10 backdrop-blur-xl border border-white/20 text-slate-300 hover:text-white rounded-xl font-medium transition-all duration-300 hover:bg-white/20 hover:scale-105 hover:shadow-lg hover:shadow-cyan-500/50 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2 focus:ring-offset-slate-900"
            >
              <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
              </svg>
              Edit Collection
            </a>
          </div>
        </div>
      </div>

      <!-- Collection Items Grid -->
      <div v-if="hasImages" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-12">
        <div
          v-for="(image, index) in imagesList"
          :key="image.id"
          class="group relative aspect-square bg-slate-800/50 backdrop-blur-xl border border-white/10 rounded-2xl overflow-hidden shadow-xl transition-all duration-500 hover:scale-105 hover:shadow-2xl hover:shadow-violet-500/30 hover:border-white/30 animate-fade-in-up"
          :style="`animation-delay: ${index * 0.1}s`"
        >
          <a :href="`/images/${image.slug}`">
            <img
              :src="getImageUrl(image)"
              :alt="image.alt_text || image.title"
              class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
              loading="lazy"
              @error="handleImageError"
            />
          </a>
          
          <!-- Remove Button -->
          <button
            v-if="canEdit"
            @click="removeFromCollection(image.id)"
            class="absolute top-3 right-3 p-2 bg-red-500/90 backdrop-blur-xl text-white rounded-full opacity-0 group-hover:opacity-100 transition-all duration-300 hover:bg-red-600 hover:scale-110 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:ring-offset-slate-900"
            title="Remove from collection"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>

          <!-- Image Info -->
          <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent p-4 backdrop-blur-sm">
            <h3 class="text-white font-medium truncate text-sm">{{ image.title }}</h3>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-20 bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl animate-fade-in-up">
        <div class="text-6xl mb-6 animate-bounce">
          <svg class="w-20 h-20 mx-auto text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
          </svg>
        </div>
        <h3 class="text-2xl font-semibold text-slate-300 mb-3">No images in this collection</h3>
        <p class="text-slate-400 mb-8 max-w-md mx-auto">
          <span v-if="canEdit">Ready to showcase your work? Add some stunning images to get started.</span>
          <span v-else>This collection is waiting for beautiful content to be added.</span>
        </p>
        <button
          v-if="canEdit"
          @click="openAddModal"
          class="px-8 py-4 bg-gradient-to-r from-violet-600 to-purple-600 text-white rounded-xl font-medium transition-all duration-300 hover:from-violet-700 hover:to-purple-700 hover:scale-105 hover:shadow-lg hover:shadow-violet-500/50 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-offset-2 focus:ring-offset-slate-900"
        >
          <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
          </svg>
          Add Your First Image
        </button>
      </div>

      <!-- Simple Pagination -->
      <div v-if="showPagination" class="flex justify-center space-x-3 mt-12 animate-fade-in-up">
        <a
          v-for="link in paginationLinks"
          :key="link.label"
          :href="link.url || '#'"
          :class="[
            'px-4 py-2 text-sm rounded-xl font-medium transition-all duration-300',
            link.active 
              ? 'bg-gradient-to-r from-violet-600 to-purple-600 text-white shadow-lg shadow-violet-500/50' 
              : 'bg-white/10 backdrop-blur-xl border border-white/20 text-slate-300 hover:text-white hover:bg-white/20 hover:scale-105',
            !link.url ? 'opacity-50 cursor-not-allowed' : ''
          ]"
          v-html="link.label"
        />
      </div>
    </div>

    <!-- Enhanced Add Images Modal -->
    <div v-if="showAddModal" class="fixed inset-0 bg-black/60 backdrop-blur-xl flex items-center justify-center z-50 p-4 animate-fade-in" @click="closeModal">
      <div class="bg-slate-900/95 backdrop-blur-xl border border-white/20 rounded-2xl p-8 max-w-6xl w-full max-h-[85vh] overflow-y-auto shadow-2xl shadow-violet-500/20 animate-scale-up" @click.stop>
        <div class="flex justify-between items-center mb-8">
          <h3 class="text-2xl font-bold bg-gradient-to-r from-violet-400 to-cyan-400 bg-clip-text text-transparent">Add Images to Collection</h3>
          <button
            @click="closeModal"
            class="p-2 text-slate-400 hover:text-white bg-white/10 backdrop-blur-xl border border-white/20 rounded-xl transition-all duration-300 hover:bg-white/20 hover:scale-110 focus:outline-none focus:ring-2 focus:ring-violet-500"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <!-- Search Images -->
        <div class="mb-8">
          <div class="relative">
            <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input
              v-model="searchQuery"
              @input="searchImages"
              type="text"
              placeholder="Search your images..."
              class="w-full pl-12 pr-4 py-4 bg-white/10 backdrop-blur-xl border border-white/20 text-slate-300 placeholder-slate-400 rounded-xl transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent hover:bg-white/15"
            />
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="loadingImages" class="text-center py-16">
          <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-violet-500 mb-4"></div>
          <div class="text-slate-400 text-lg">Loading your amazing images...</div>
        </div>

        <!-- Available Images Grid -->
        <div v-else-if="availableImages.length > 0" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-8 max-h-96 overflow-y-auto pr-2">
          <div
            v-for="image in availableImages"
            :key="image.id"
            @click="toggleImageSelection(image)"
            :class="[
              'relative aspect-square bg-slate-800/50 backdrop-blur-xl rounded-xl overflow-hidden cursor-pointer border-2 transition-all duration-300 hover:scale-105',
              selectedImages.includes(image.id) 
                ? 'border-violet-500 ring-4 ring-violet-500/30 shadow-lg shadow-violet-500/50' 
                : 'border-white/10 hover:border-white/30'
            ]"
          >
            <img
              :src="getImageUrl(image)"
              :alt="image.title"
              class="w-full h-full object-cover transition-transform duration-300"
              loading="lazy"
              @error="handleImageError"
            />
            
            <!-- Selection indicator -->
            <div
              v-if="selectedImages.includes(image.id)"
              class="absolute top-2 right-2 bg-gradient-to-r from-violet-500 to-purple-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold shadow-lg animate-bounce"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
            </div>
            
            <!-- Image title -->
            <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/80 to-transparent p-2 backdrop-blur-sm">
              <p class="text-white text-xs truncate font-medium">{{ image.title }}</p>
            </div>
          </div>
        </div>

        <!-- No images found -->
        <div v-else class="text-center py-16">
          <div class="text-6xl mb-6 text-slate-600">
            <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
          </div>
          <p class="text-slate-400 text-lg">No images found. Try adjusting your search or upload some images first.</p>
        </div>

        <!-- Modal Actions -->
        <div class="flex justify-between items-center pt-6 border-t border-white/10">
          <p class="text-slate-400 font-medium">
            <span class="text-violet-400 font-bold">{{ selectedImages.length }}</span> image(s) selected
          </p>
          
          <div class="flex space-x-4">
            <button
              @click="closeModal"
              class="px-6 py-3 text-slate-300 bg-white/10 backdrop-blur-xl border border-white/20 rounded-xl font-medium transition-all duration-300 hover:bg-white/20 hover:text-white focus:outline-none focus:ring-2 focus:ring-slate-500"
            >
              Cancel
            </button>
            <button
              @click="addSelectedImages"
              :disabled="selectedImages.length === 0 || addingImages"
              :class="[
                'px-6 py-3 text-white rounded-xl font-medium transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-violet-500',
                selectedImages.length > 0 && !addingImages
                  ? 'bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-700 hover:to-purple-700 hover:scale-105 hover:shadow-lg hover:shadow-violet-500/50'
                  : 'bg-slate-600 cursor-not-allowed opacity-50'
              ]"
            >
              <span v-if="addingImages" class="flex items-center">
                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                Adding...
              </span>
              <span v-else>Add {{ selectedImages.length }} Image(s)</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  collection: Object,
  images: Object,
  can: Object,
})

// Reactive state
const showAddModal = ref(false)
const availableImages = ref([])
const selectedImages = ref([])
const searchQuery = ref('')
const loadingImages = ref(false)
const addingImages = ref(false)

// Computed properties
const canEdit = computed(() => props.can?.update || false)

const hasImages = computed(() => {
  return props.images?.data && props.images.data.length > 0
})

const imagesList = computed(() => {
  return props.images?.data || []
})

const showPagination = computed(() => {
  return props.images?.links && props.images.links.length > 3
})

const paginationLinks = computed(() => {
  return props.images?.links || []
})

// Methods
const openAddModal = async () => {
  showAddModal.value = true
  selectedImages.value = []
  searchQuery.value = ''
  await loadAvailableImages()
}

const closeModal = () => {
  showAddModal.value = false
  selectedImages.value = []
  availableImages.value = []
  searchQuery.value = ''
}

const loadAvailableImages = async () => {
  loadingImages.value = true
  try {
    const response = await fetch('/api/my-images?limit=50')
    const data = await response.json()
    availableImages.value = data.images || []
  } catch (error) {
    console.error('Failed to load images:', error)
    availableImages.value = []
    alert('Failed to load images. Please try again.')
  } finally {
    loadingImages.value = false
  }
}

const searchImages = async () => {
  if (!searchQuery.value.trim()) {
    await loadAvailableImages()
    return
  }

  loadingImages.value = true
  try {
    const response = await fetch(`/api/search-images?q=${encodeURIComponent(searchQuery.value)}`)
    const data = await response.json()
    availableImages.value = data.images || []
  } catch (error) {
    console.error('Failed to search images:', error)
    availableImages.value = []
  } finally {
    loadingImages.value = false
  }
}

const toggleImageSelection = (image) => {
  const index = selectedImages.value.indexOf(image.id)
  if (index > -1) {
    selectedImages.value.splice(index, 1)
  } else {
    selectedImages.value.push(image.id)
  }
}

const addSelectedImages = async () => {
  if (selectedImages.value.length === 0) return

  addingImages.value = true
  try {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    
    // Add each selected image to the collection
    const promises = selectedImages.value.map(imageId => 
      fetch(`/collections/${props.collection.slug}/add-image`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': token || ''
        },
        body: JSON.stringify({
          image_id: imageId
        })
      })
    )

    await Promise.all(promises)
    
    // Close modal and reload page
    closeModal()
    window.location.reload()
    
  } catch (error) {
    console.error('Failed to add images:', error)
    alert('Failed to add images to collection. Please try again.')
  } finally {
    addingImages.value = false
  }
}

const removeFromCollection = (imageId) => {
  if (confirm('Are you sure you want to remove this image from the collection?')) {
    // Simple form submission
    const form = document.createElement('form')
    form.method = 'POST'
    form.action = `/collections/${props.collection.slug}/remove-image`
    
    const methodInput = document.createElement('input')
    methodInput.type = 'hidden'
    methodInput.name = '_method'
    methodInput.value = 'DELETE'
    form.appendChild(methodInput)
    
    const tokenInput = document.createElement('input')
    tokenInput.type = 'hidden'
    tokenInput.name = '_token'
    tokenInput.value = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
    form.appendChild(tokenInput)
    
    const imageInput = document.createElement('input')
    imageInput.type = 'hidden'
    imageInput.name = 'image_id'
    imageInput.value = imageId
    form.appendChild(imageInput)
    
    document.body.appendChild(form)
    form.submit()
  }
}

const getImageUrl = (image) => {
  // Try different image URL patterns
  if (image?.thumbnail_url) {
    return image.thumbnail_url
  }
  
  if (image?.storage_path) {
    // Try MinIO URL first (based on your previous setup)
    return `http://localhost:9000/gallery-images/${image.storage_path}`
  }
  
  // Fallback to Laravel storage
  if (image?.storage_path) {
    return `/storage/${image.storage_path}`
  }
  
  // Ultimate fallback
  return '/images/placeholder.jpg'
}

const handleImageError = (event) => {
  // Try fallback URLs when image fails to load
  const img = event.target
  const currentSrc = img.src
  
  if (currentSrc.includes('localhost:9000')) {
    // Try Laravel storage path
    const imagePath = currentSrc.split('/').pop()
    img.src = `/storage/${imagePath}`
  } else if (currentSrc.includes('/storage/')) {
    // Use placeholder
    img.src = '/images/placeholder.jpg'
  }
}

const formatDate = (date) => {
  if (!date) return 'Unknown date'
  
  try {
    return new Date(date).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    })
  } catch (e) {
    return 'Invalid date'
  }
}
</script>

<style scoped>
@keyframes shimmer {
  0% {
    background-position: -200% 0;
  }
  100% {
    background-position: 200% 0;
  }
}

@keyframes fade-in-up {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes fade-in {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

@keyframes scale-up {
  from {
    opacity: 0;
    transform: scale(0.9);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

.animate-shimmer {
  animation: shimmer 3s ease-in-out infinite;
}

.animate-fade-in-up {
  animation: fade-in-up 0.6s ease-out forwards;
  opacity: 0;
}

.animate-fade-in {
  animation: fade-in 0.3s ease-out forwards;
}

.animate-scale-up {
  animation: scale-up 0.3s ease-out forwards;
}

/* Custom scrollbar for modal */
.overflow-y-auto::-webkit-scrollbar {
  width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: rgba(139, 92, 246, 0.5);
  border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: rgba(139, 92, 246, 0.8);
}

/* Gradient animation for mesh background */
@keyframes gradient-shift {
  0% {
    stop-color: #8b5cf6;
  }
  33% {
    stop-color: #06b6d4;
  }
  66% {
    stop-color: #a855f7;
  }
  100% {
    stop-color: #8b5cf6;
  }
}

#meshGradient stop {
  animation: gradient-shift 8s ease-in-out infinite;
}

/* Enhanced hover effects */
.group:hover .group-hover\:scale-110 {
  transform: scale(1.1);
}

.group:hover .group-hover\:opacity-100 {
  opacity: 1;
}

/* Glass morphism enhancement */
.backdrop-blur-xl {
  backdrop-filter: blur(16px);
}

/* Custom button glow effects */
.hover\:shadow-violet-500\/50:hover {
  box-shadow: 0 10px 25px -5px rgba(139, 92, 246, 0.5), 0 10px 10px -5px rgba(139, 92, 246, 0.04);
}

.hover\:shadow-cyan-500\/50:hover {
  box-shadow: 0 10px 25px -5px rgba(6, 182, 212, 0.5), 0 10px 10px -5px rgba(6, 182, 212, 0.04);
}

.hover\:shadow-violet-500\/30:hover {
  box-shadow: 0 20px 25px -5px rgba(139, 92, 246, 0.3), 0 10px 10px -5px rgba(139, 92, 246, 0.04);
}

/* Floating particles animation enhancement */
.animate-bounce {
  animation: bounce 1s infinite;
}

@keyframes bounce {
  0%, 100% {
    transform: translateY(-25%);
    animation-timing-function: cubic-bezier(0.8, 0, 1, 1);
  }
  50% {
    transform: translateY(0);
    animation-timing-function: cubic-bezier(0, 0, 0.2, 1);
  }
}

/* Enhanced ring effects */
.ring-4 {
  --tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);
  --tw-ring-shadow: var(--tw-ring-inset) 0 0 0 calc(4px + var(--tw-ring-offset-width)) var(--tw-ring-color);
  box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow, 0 0 #0000);
}

.ring-violet-500\/30 {
  --tw-ring-color: rgba(139, 92, 246, 0.3);
}

/* Improved focus states */
.focus\:ring-2:focus {
  --tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);
  --tw-ring-shadow: var(--tw-ring-inset) 0 0 0 calc(2px + var(--tw-ring-offset-width)) var(--tw-ring-color);
  box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow, 0 0 #0000);
}

.focus\:ring-violet-500:focus {
  --tw-ring-color: #8b5cf6;
}

.focus\:ring-cyan-500:focus {
  --tw-ring-color: #06b6d4;
}

.focus\:ring-red-500:focus {
  --tw-ring-color: #ef4444;
}

.focus\:ring-slate-500:focus {
  --tw-ring-color: #64748b;
}

.focus\:ring-offset-2:focus {
  --tw-ring-offset-width: 2px;
}

.focus\:ring-offset-slate-900:focus {
  --tw-ring-offset-color: #0f172a;
}
</style>