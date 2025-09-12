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
