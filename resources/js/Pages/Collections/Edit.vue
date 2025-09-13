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
/*<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">
    <!-- Animated Background Elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
      <!-- Animated Mesh Background -->
      <div class="absolute inset-0 opacity-20">
        <svg class="absolute inset-0 w-full h-full animate-pulse" viewBox="0 0 1000 1000">
          <defs>
            <linearGradient id="meshGradient" x1="0%" y1="0%" x2="100%" y2="100%">
              <stop offset="0%" class="animate-[colorShift_8s_ease-in-out_infinite] stop-color-violet-500" />
              <stop offset="50%" class="animate-[colorShift_8s_ease-in-out_infinite_2s] stop-color-cyan-500" />
              <stop offset="100%" class="animate-[colorShift_8s_ease-in-out_infinite_4s] stop-color-purple-500" />
            </linearGradient>
          </defs>
          <path d="M0,100 Q250,50 500,100 T1000,100 L1000,200 Q750,150 500,200 T0,200 Z" fill="url(#meshGradient)" opacity="0.1" class="animate-[wave_6s_ease-in-out_infinite]" />
          <path d="M0,300 Q250,250 500,300 T1000,300 L1000,400 Q750,350 500,400 T0,400 Z" fill="url(#meshGradient)" opacity="0.05" class="animate-[wave_8s_ease-in-out_infinite_reverse]" />
        </svg>
      </div>

      <!-- Floating Particles -->
      <div class="absolute inset-0">
        <div v-for="i in 20" :key="i" 
             class="absolute w-1 h-1 bg-gradient-to-r from-violet-400 to-cyan-400 rounded-full animate-float"
             :style="{
               left: Math.random() * 100 + '%',
               top: Math.random() * 100 + '%',
               animationDelay: Math.random() * 10 + 's',
               animationDuration: (3 + Math.random() * 4) + 's'
             }">
        </div>
      </div>
    </div>

    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      
      <!-- Enhanced Header -->
      <div class="mb-8 animate-fade-in-up">
        <div class="flex items-center justify-between">
          <div class="space-y-3">
            <a href="/collections" 
               class="inline-flex items-center text-cyan-400 hover:text-cyan-300 transition-colors duration-300 group">
              <svg class="h-4 w-4 mr-2 transform group-hover:-translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
              </svg>
              Back to Collections
            </a>
            <h1 class="text-4xl font-bold bg-gradient-to-r from-violet-400 via-purple-400 to-cyan-400 bg-clip-text text-transparent animate-shimmer">
              Edit Collection
            </h1>
            <p class="text-slate-300 text-lg">Update your collection details</p>
          </div>
          
          <a
            :href="`/collections/${collection.slug}`"
            class="group relative px-6 py-3 bg-gradient-to-r from-slate-700 to-slate-600 text-slate-200 rounded-xl hover:from-slate-600 hover:to-slate-500 transition-all duration-300 shadow-lg shadow-slate-900/50 hover:shadow-xl hover:shadow-slate-900/70 hover:scale-105 border border-slate-600/30"
          >
            <div class="absolute inset-0 bg-gradient-to-r from-slate-600 to-slate-500 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <span class="relative z-10 font-medium">View Collection</span>
          </a>
        </div>
      </div>

      <!-- Enhanced Edit Form -->
      <div class="relative bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl shadow-black/20 overflow-hidden animate-fade-in-up" style="animation-delay: 0.2s;">
        <div class="absolute inset-0 bg-gradient-to-br from-violet-500/5 via-transparent to-cyan-500/5"></div>
        <div class="relative p-8">
          <form @submit="handleSubmit">
            
            <!-- Enhanced Title Field -->
            <div class="mb-8 space-y-3">
              <label for="title" class="block text-sm font-semibold text-slate-200">
                Collection Title *
              </label>
              <div class="relative">
                <input
                  id="title"
                  v-model="form.title"
                  type="text"
                  required
                  class="w-full px-4 py-4 bg-slate-800/50 border rounded-xl text-slate-200 placeholder-slate-400 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-300 backdrop-blur-sm"
                  :class="errors.title ? 'border-red-400/50' : 'border-slate-600/30'"
                  placeholder="Enter collection title"
                />
                <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-violet-500/10 to-cyan-500/10 opacity-0 hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
              </div>
              <p v-if="errors.title" class="text-sm text-red-300 flex items-center">
                <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                {{ errors.title }}
              </p>
            </div>

            <!-- Enhanced Description Field -->
            <div class="mb-8 space-y-3">
              <label for="description" class="block text-sm font-semibold text-slate-200">
                Description
              </label>
              <div class="relative">
                <textarea
                  id="description"
                  v-model="form.description"
                  rows="4"
                  class="w-full px-4 py-4 bg-slate-800/50 border rounded-xl text-slate-200 placeholder-slate-400 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-300 backdrop-blur-sm resize-none"
                  :class="errors.description ? 'border-red-400/50' : 'border-slate-600/30'"
                  placeholder="Describe your collection..."
                ></textarea>
                <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-violet-500/10 to-cyan-500/10 opacity-0 hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
              </div>
              <p v-if="errors.description" class="text-sm text-red-300 flex items-center">
                <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                {{ errors.description }}
              </p>
            </div>

            <!-- Enhanced Privacy Settings -->
            <div class="mb-8 space-y-4">
              <label class="block text-sm font-semibold text-slate-200">
                Privacy Settings *
              </label>
              <div class="space-y-4">
                <!-- Public Option -->
                <div class="relative group">
                  <div class="flex items-center p-4 bg-slate-800/30 border border-slate-700/30 rounded-xl hover:border-emerald-400/30 hover:bg-slate-800/50 transition-all duration-300">
                    <input
                      id="privacy-public"
                      v-model="form.privacy"
                      type="radio"
                      value="public"
                      class="h-5 w-5 text-emerald-500 focus:ring-emerald-500 border-slate-500 bg-slate-800/50"
                    />
                    <label for="privacy-public" class="ml-4 flex-1 cursor-pointer">
                      <div class="flex items-center">
                        <span class="block text-base font-medium text-slate-200 group-hover:text-emerald-300 transition-colors duration-300">Public</span>
                        <svg class="h-5 w-5 text-emerald-400 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                      </div>
                      <span class="block text-sm text-slate-400 mt-1">Anyone can view this collection</span>
                    </label>
                  </div>
                </div>
                
                <!-- Unlisted Option -->
                <div class="relative group">
                  <div class="flex items-center p-4 bg-slate-800/30 border border-slate-700/30 rounded-xl hover:border-amber-400/30 hover:bg-slate-800/50 transition-all duration-300">
                    <input
                      id="privacy-unlisted"
                      v-model="form.privacy"
                      type="radio"
                      value="unlisted"
                      class="h-5 w-5 text-amber-500 focus:ring-amber-500 border-slate-500 bg-slate-800/50"
                    />
                    <label for="privacy-unlisted" class="ml-4 flex-1 cursor-pointer">
                      <div class="flex items-center">
                        <span class="block text-base font-medium text-slate-200 group-hover:text-amber-300 transition-colors duration-300">Unlisted</span>
                        <svg class="h-5 w-5 text-amber-400 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                        </svg>
                      </div>
                      <span class="block text-sm text-slate-400 mt-1">Only people with the link can view</span>
                    </label>
                  </div>
                </div>
                
                <!-- Private Option -->
                <div class="relative group">
                  <div class="flex items-center p-4 bg-slate-800/30 border border-slate-700/30 rounded-xl hover:border-red-400/30 hover:bg-slate-800/50 transition-all duration-300">
                    <input
                      id="privacy-private"
                      v-model="form.privacy"
                      type="radio"
                      value="private"
                      class="h-5 w-5 text-red-500 focus:ring-red-500 border-slate-500 bg-slate-800/50"
                    />
                    <label for="privacy-private" class="ml-4 flex-1 cursor-pointer">
                      <div class="flex items-center">
                        <span class="block text-base font-medium text-slate-200 group-hover:text-red-300 transition-colors duration-300">Private</span>
                        <svg class="h-5 w-5 text-red-400 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                      </div>
                      <span class="block text-sm text-slate-400 mt-1">Only you can view this collection</span>
                    </label>
                  </div>
                </div>
              </div>
              <p v-if="errors.privacy" class="text-sm text-red-300 flex items-center">
                <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                {{ errors.privacy }}
              </p>
            </div>

            <!-- Enhanced Collection Stats -->
            <div class="mb-8">
              <div class="relative bg-slate-800/20 backdrop-blur-sm border border-slate-700/30 rounded-xl p-6">
                <div class="absolute inset-0 bg-gradient-to-r from-violet-500/5 to-cyan-500/5 rounded-xl"></div>
                <div class="relative">
                  <h3 class="text-lg font-semibold text-slate-200 mb-4 flex items-center">
                    <svg class="h-5 w-5 text-violet-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Collection Statistics
                  </h3>
                  <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div class="flex items-center space-x-3">
                      <div class="flex-shrink-0 p-2 bg-cyan-500/20 rounded-lg">
                        <svg class="h-5 w-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                      </div>
                      <div>
                        <span class="text-slate-400 text-sm">Images</span>
                        <div class="text-white font-semibold text-lg">{{ collection.images_count || 0 }}</div>
                      </div>
                    </div>
                    <div class="flex items-center space-x-3">
                      <div class="flex-shrink-0 p-2 bg-purple-500/20 rounded-lg">
                        <svg class="h-5 w-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                      </div>
                      <div>
                        <span class="text-slate-400 text-sm">Created</span>
                        <div class="text-white font-semibold">{{ formatDate(collection.created_at) }}</div>
                      </div>
                    </div>
                    <div class="flex items-center space-x-3">
                      <div class="flex-shrink-0 p-2 bg-violet-500/20 rounded-lg">
                        <svg class="h-5 w-5 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                      </div>
                      <div>
                        <span class="text-slate-400 text-sm">Updated</span>
                        <div class="text-white font-semibold">{{ formatDate(collection.updated_at) }}</div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Enhanced Publishing Status -->
            <div class="mb-8">
              <div class="relative bg-slate-800/20 backdrop-blur-sm border border-slate-700/30 rounded-xl p-6">
                <div class="absolute inset-0 bg-gradient-to-r from-emerald-500/5 to-blue-500/5 rounded-xl"></div>
                <div class="relative flex items-center justify-between">
                  <div class="space-y-2">
                    <h3 class="text-lg font-semibold text-slate-200 flex items-center">
                      <svg class="h-5 w-5 text-emerald-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      </svg>
                      Publishing Status
                    </h3>
                    <p class="text-slate-300 flex items-center space-x-2">
                      <span v-if="collection.is_published" 
                            class="inline-flex items-center px-2 py-1 bg-emerald-500/20 text-emerald-300 rounded-full text-sm border border-emerald-400/30">
                        <svg class="h-3 w-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        Published
                      </span>
                      <span v-else 
                            class="inline-flex items-center px-2 py-1 bg-amber-500/20 text-amber-300 rounded-full text-sm border border-amber-400/30">
                        <svg class="h-3 w-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                        </svg>
                        Draft
                      </span>
                      <span class="text-slate-400">-</span>
                      <span class="text-slate-400">
                        {{ form.privacy === 'private' ? 'Will remain private' : 'Will be published when saved' }}
                      </span>
                    </p>
                  </div>
                  
                  <div>
                    <button
                      type="button"
                      @click="togglePublishStatus"
                      :disabled="submitting"
                      class="group relative px-6 py-3 rounded-xl font-medium transition-all duration-300 shadow-lg hover:scale-105 border"
                      :class="[
                        collection.is_published
                          ? 'bg-gradient-to-r from-amber-500/20 to-orange-500/20 text-amber-300 border-amber-400/30 hover:from-amber-500/30 hover:to-orange-500/30 shadow-amber-500/20'
                          : 'bg-gradient-to-r from-emerald-500/20 to-green-500/20 text-emerald-300 border-emerald-400/30 hover:from-emerald-500/30 hover:to-green-500/30 shadow-emerald-500/20',
                        submitting ? 'opacity-50 cursor-not-allowed' : ''
                      ]"
                    >
                      {{ collection.is_published ? 'Unpublish' : 'Publish' }}
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Enhanced Form Actions -->
            <div class="flex items-center justify-between pt-8 border-t border-slate-700/30">
              <button
                type="button"
                @click="showDeleteModal = true"
                class="group relative px-6 py-3 bg-gradient-to-r from-red-500/20 to-red-600/20 text-red-300 border border-red-400/30 rounded-xl font-medium hover:from-red-500/30 hover:to-red-600/30 transition-all duration-300 shadow-lg shadow-red-500/20 hover:scale-105"
              >
                <svg class="h-4 w-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Delete Collection
              </button>
              
              <div class="flex space-x-4">
                <a
                  :href="`/collections/${collection.slug}`"
                  class="px-6 py-3 bg-slate-700/50 text-slate-300 border border-slate-600/30 rounded-xl font-medium hover:bg-slate-600/50 hover:text-white transition-all duration-300 backdrop-blur-sm"
                >
                  Cancel
                </a>
                <button
                  type="submit"
                  :disabled="submitting"
                  class="group relative px-8 py-3 bg-gradient-to-r from-violet-500 to-purple-500 text-white rounded-xl font-semibold shadow-lg shadow-violet-500/25 hover:shadow-xl hover:shadow-violet-500/40 hover:scale-105 transition-all duration-300 border border-violet-400/20"
                  :class="submitting ? 'opacity-50 cursor-not-allowed' : ''"
                >
                  <div class="absolute inset-0 bg-gradient-to-r from-violet-600 to-purple-600 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                  <span class="relative z-10 flex items-center">
                    <svg v-if="submitting" class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ submitting ? 'Saving...' : 'Save Changes' }}
                  </span>
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Enhanced Delete Confirmation Modal -->
    <div v-if="showDeleteModal" 
         class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 animate-fade-in" 
         @click="showDeleteModal = false">
      <div class="relative bg-slate-800/90 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-8 max-w-md w-full mx-4 shadow-2xl shadow-black/50 animate-scale-in" 
           @click.stop>
        <div class="absolute inset-0 bg-gradient-to-br from-red-500/10 via-transparent to-red-600/10 rounded-2xl"></div>
        <div class="relative space-y-6">
          <div class="flex items-center space-x-3">
            <div class="flex-shrink-0 p-2 bg-red-500/20 rounded-lg">
              <svg class="h-6 w-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z"></path>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-white">Delete Collection</h3>
          </div>
          
          <p class="text-slate-300 leading-relaxed">
            Are you sure you want to delete <span class="font-semibold text-white">"{{ collection.title }}"</span>? 
            This action cannot be undone. The images in this collection will not be deleted.
          </p>
          
          <div class="flex justify-end space-x-4 pt-4">
            <button
              @click="showDeleteModal = false"
              class="px-6 py-3 bg-slate-700/50 text-slate-300 border border-slate-600/30 rounded-xl font-medium hover:bg-slate-600/50 hover:text-white transition-all duration-300"
            >
              Cancel
            </button>
            <button
              @click="deleteCollection"
              :disabled="deleting"
              class="group relative px-6 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl font-semibold shadow-lg shadow-red-500/25 hover:shadow-xl hover:shadow-red-500/40 hover:scale-105 transition-all duration-300"
              :class="deleting ? 'opacity-50 cursor-not-allowed' : ''"
            >
              <div class="absolute inset-0 bg-gradient-to-r from-red-600 to-red-700 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
              <span class="relative z-10 flex items-center">
                <svg v-if="deleting" class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ deleting ? 'Deleting...' : 'Delete Collection' }}
              </span>
            </button>
          </div>
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

<style scoped>
@keyframes shimmer {
  0%, 100% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
}

@keyframes colorShift {
  0%, 100% { stop-color: rgb(139 92 246); }
  33% { stop-color: rgb(168 85 247); }
  66% { stop-color: rgb(34 211 238); }
}

@keyframes wave {
  0%, 100% { transform: translateX(0px) translateY(0px); }
  50% { transform: translateX(10px) translateY(-5px); }
}

@keyframes float {
  0%, 100% { 
    transform: translateY(0px) rotate(0deg);
    opacity: 0.4;
  }
  50% { 
    transform: translateY(-20px) rotate(180deg);
    opacity: 0.8;
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
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes scale-in {
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
  background-size: 200% 200%;
  animation: shimmer 3s ease-in-out infinite;
}

.animate-fade-in-up {
  animation: fade-in-up 0.6s ease-out forwards;
}

.animate-fade-in {
  animation: fade-in 0.5s ease-out forwards;
}

.animate-scale-in {
  animation: scale-in 0.3s ease-out forwards;
}

.animate-float {
  animation: float 6s ease-in-out infinite;
}

/* Custom Scrollbar */
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  @apply bg-slate-800/50;
}

::-webkit-scrollbar-thumb {
  @apply bg-gradient-to-b from-violet-500 to-purple-500 rounded-full;
}

::-webkit-scrollbar-thumb:hover {
  @apply from-violet-400 to-purple-400;
}
</style>