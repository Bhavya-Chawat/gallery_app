<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      
      <!-- Header -->
      <div class="mb-8">
        <div class="flex items-center justify-between">
          <div>
            <a href="/collections" class="text-blue-600 hover:text-blue-800 mb-2 inline-block">
              ← Back to Collections
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Edit Collection</h1>
            <p class="text-gray-600 mt-1">Update your collection details</p>
          </div>
          
          <a
            :href="`/collections/${collection.slug}`"
            class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700"
          >
            View Collection
          </a>
        </div>
      </div>

      <!-- Edit Form -->
      <div class="bg-white rounded-lg shadow p-6">
        <form @submit="handleSubmit">
          
          <!-- Title -->
          <div class="mb-6">
            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
              Collection Title *
            </label>
            <input
              id="title"
              v-model="form.title"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
              :class="{ 'border-red-300': errors.title }"
              placeholder="Enter collection title"
            />
            <p v-if="errors.title" class="mt-1 text-sm text-red-600">{{ errors.title }}</p>
          </div>

          <!-- Description -->
          <div class="mb-6">
            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
              Description
            </label>
            <textarea
              id="description"
              v-model="form.description"
              rows="4"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
              :class="{ 'border-red-300': errors.description }"
              placeholder="Describe your collection..."
            ></textarea>
            <p v-if="errors.description" class="mt-1 text-sm text-red-600">{{ errors.description }}</p>
          </div>

          <!-- Privacy Settings -->
          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-3">
              Privacy Settings *
            </label>
            <div class="space-y-3">
              <div class="flex items-center">
                <input
                  id="privacy-public"
                  v-model="form.privacy"
                  type="radio"
                  value="public"
                  class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                />
                <label for="privacy-public" class="ml-3 flex-1">
                  <span class="block text-sm font-medium text-gray-900">Public</span>
                  <span class="block text-sm text-gray-500">Anyone can view this collection</span>
                </label>
              </div>
              
              <div class="flex items-center">
                <input
                  id="privacy-unlisted"
                  v-model="form.privacy"
                  type="radio"
                  value="unlisted"
                  class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                />
                <label for="privacy-unlisted" class="ml-3 flex-1">
                  <span class="block text-sm font-medium text-gray-900">Unlisted</span>
                  <span class="block text-sm text-gray-500">Only people with the link can view</span>
                </label>
              </div>
              
              <div class="flex items-center">
                <input
                  id="privacy-private"
                  v-model="form.privacy"
                  type="radio"
                  value="private"
                  class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                />
                <label for="privacy-private" class="ml-3 flex-1">
                  <span class="block text-sm font-medium text-gray-900">Private</span>
                  <span class="block text-sm text-gray-500">Only you can view this collection</span>
                </label>
              </div>
            </div>
            <p v-if="errors.privacy" class="mt-1 text-sm text-red-600">{{ errors.privacy }}</p>
          </div>

          <!-- Collection Stats -->
          <div class="mb-6 p-4 bg-gray-50 rounded-lg">
            <h3 class="text-sm font-medium text-gray-900 mb-2">Collection Statistics</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
              <div>
                <span class="text-gray-500">Images:</span>
                <span class="font-medium text-gray-900 ml-1">{{ collection.images_count || 0 }}</span>
              </div>
              <div>
                <span class="text-gray-500">Created:</span>
                <span class="font-medium text-gray-900 ml-1">{{ formatDate(collection.created_at) }}</span>
              </div>
              <div>
                <span class="text-gray-500">Updated:</span>
                <span class="font-medium text-gray-900 ml-1">{{ formatDate(collection.updated_at) }}</span>
              </div>
            </div>
          </div>

          <!-- Publishing Status -->
          <div class="mb-8 p-4 border rounded-lg">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-sm font-medium text-gray-900">Publishing Status</h3>
                <p class="text-sm text-gray-500 mt-1">
                  <span v-if="collection.is_published" class="text-green-600">✓ Published</span>
                  <span v-else class="text-yellow-600">○ Draft</span>
                  - {{ form.privacy === 'private' ? 'Will remain private' : 'Will be published when saved' }}
                </p>
              </div>
              
              <div class="text-right">
                <button
                  type="button"
                  @click="togglePublishStatus"
                  :disabled="submitting"
                  :class="[
                    'px-4 py-2 text-sm font-medium rounded-md',
                    collection.is_published
                      ? 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200'
                      : 'bg-green-100 text-green-800 hover:bg-green-200',
                    submitting ? 'opacity-50 cursor-not-allowed' : ''
                  ]"
                >
                  {{ collection.is_published ? 'Unpublish' : 'Publish' }}
                </button>
              </div>
            </div>
          </div>

          <!-- Form Actions -->
          <div class="flex items-center justify-between pt-6 border-t">
            <button
              type="button"
              @click="showDeleteModal = true"
              class="px-4 py-2 text-sm font-medium text-red-600 bg-white border border-red-300 rounded-md hover:bg-red-50"
            >
              Delete Collection
            </button>
            
            <div class="flex space-x-3">
              <a
                :href="`/collections/${collection.slug}`"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
              >
                Cancel
              </a>
              <button
                type="submit"
                :disabled="submitting"
                :class="[
                  'px-6 py-2 text-sm font-medium text-white rounded-md',
                  submitting
                    ? 'bg-gray-400 cursor-not-allowed'
                    : 'bg-blue-600 hover:bg-blue-700'
                ]"
              >
                {{ submitting ? 'Saving...' : 'Save Changes' }}
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click="showDeleteModal = false">
      <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4" @click.stop>
        <h3 class="text-lg font-medium text-gray-900 mb-4">Delete Collection</h3>
        <p class="text-gray-600 mb-6">
          Are you sure you want to delete "{{ collection.title }}"? This action cannot be undone.
          The images in this collection will not be deleted.
        </p>
        
        <div class="flex justify-end space-x-3">
          <button
            @click="showDeleteModal = false"
            class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
          >
            Cancel
          </button>
          <button
            @click="deleteCollection"
            :disabled="deleting"
            :class="[
              'px-4 py-2 text-white rounded-md',
              deleting
                ? 'bg-gray-400 cursor-not-allowed'
                : 'bg-red-600 hover:bg-red-700'
            ]"
          >
            {{ deleting ? 'Deleting...' : 'Delete Collection' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>

import { ref, reactive, onMounted } from 'vue'

// Fix CSRF token immediately
onMounted(() => {
  if (!document.querySelector('meta[name="csrf-token"]')) {
    const meta = document.createElement('meta')
    meta.name = 'csrf-token'
    meta.content = '{{ csrf_token() }}'
    document.head.appendChild(meta)
  }
})

const props = defineProps({
  collection: Object,
  errors: Object
})

// Reactive state  
const submitting = ref(false)
const deleting = ref(false)
const showDeleteModal = ref(false)
const errors = ref({})

// Form data
const form = reactive({
  title: props.collection.title || '',
  description: props.collection.description || '',
  privacy: props.collection.privacy || 'public'
})

// SIMPLIFIED METHODS - NO FANCY STUFF, JUST BASIC FORMS

const handleSubmit = (event) => {
  event.preventDefault()
  
  // Create basic HTML form and submit
  const htmlForm = document.createElement('form')
  htmlForm.method = 'POST'
  htmlForm.action = `/collections/${props.collection.slug}`
  
  // Add method override
  const methodField = document.createElement('input')
  methodField.type = 'hidden'
  methodField.name = '_method' 
  methodField.value = 'PATCH'
  htmlForm.appendChild(methodField)
  
  // Add CSRF token
  const csrfField = document.createElement('input')
  csrfField.type = 'hidden'
  csrfField.name = '_token'
  csrfField.value = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
  htmlForm.appendChild(csrfField)
  
  // Add form data
  const titleField = document.createElement('input')
  titleField.type = 'hidden'
  titleField.name = 'title'
  titleField.value = form.title
  htmlForm.appendChild(titleField)
  
  const descField = document.createElement('input')
  descField.type = 'hidden'
  descField.name = 'description'  
  descField.value = form.description
  htmlForm.appendChild(descField)
  
  const privacyField = document.createElement('input')
  privacyField.type = 'hidden'
  privacyField.name = 'privacy'
  privacyField.value = form.privacy
  htmlForm.appendChild(privacyField)
  
  // Submit form
  document.body.appendChild(htmlForm)
  htmlForm.submit()
}

const togglePublishStatus = () => {
  // Create basic form
  const htmlForm = document.createElement('form')
  htmlForm.method = 'POST'
  htmlForm.action = `/collections/${props.collection.slug}/toggle-publish`
  
  // Add CSRF
  const csrfField = document.createElement('input')
  csrfField.type = 'hidden'
  csrfField.name = '_token'
  csrfField.value = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
  htmlForm.appendChild(csrfField)
  
  // Submit
  document.body.appendChild(htmlForm)
  htmlForm.submit()
}

const deleteCollection = () => {
  // Create basic form  
  const htmlForm = document.createElement('form')
  htmlForm.method = 'POST'
  htmlForm.action = `/collections/${props.collection.slug}`
  
  // Add method override for DELETE
  const methodField = document.createElement('input')
  methodField.type = 'hidden'
  methodField.name = '_method'
  methodField.value = 'DELETE'
  htmlForm.appendChild(methodField)
  
  // Add CSRF
  const csrfField = document.createElement('input')
  csrfField.type = 'hidden'  
  csrfField.name = '_token'
  csrfField.value = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
  htmlForm.appendChild(csrfField)
  
  // Submit
  document.body.appendChild(htmlForm)
  htmlForm.submit()
  
  showDeleteModal.value = false
}

const formatDate = (date) => {
  if (!date) return 'Unknown'
  return new Date(date).toLocaleDateString()
}
</script>
