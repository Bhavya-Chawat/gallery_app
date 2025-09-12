<template>
  <Modal :show="show" @close="$emit('close')" max-width="lg">
    <div class="p-6">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-medium text-gray-900">Add to Collection</h3>
        <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600">
          <XMarkIcon class="h-5 w-5" />
        </button>
      </div>

      <!-- My Collections -->
      <div class="mb-6">
        <h4 class="text-sm font-medium text-gray-900 mb-3">My Collections</h4>
        
        <div v-if="loading" class="text-center py-4">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
        </div>
        
        <div v-else-if="collections.length > 0" class="space-y-2 max-h-60 overflow-y-auto">
          <div
            v-for="collection in collections"
            :key="collection.id"
            class="flex items-center justify-between p-3 border border-gray-200 rounded-md hover:bg-gray-50"
          >
            <div class="flex items-center space-x-3">
              <img
                v-if="collection.cover_image"
                :src="collection.cover_image.thumbnail_url"
                :alt="collection.title"
                class="w-10 h-10 object-cover rounded"
              />
              <FolderIcon v-else class="w-10 h-10 text-gray-400" />
              
              <div>
                <h5 class="text-sm font-medium text-gray-900">{{ collection.title }}</h5>
                <p class="text-xs text-gray-500">{{ collection.item_count }} items</p>
              </div>
            </div>
            
            <button
              @click="addToCollection(collection)"
              :disabled="isInCollection(collection) || adding"
              class="px-3 py-1 text-xs font-medium rounded-md transition-colors"
              :class="isInCollection(collection) 
                ? 'bg-green-100 text-green-800' 
                : 'bg-blue-100 text-blue-800 hover:bg-blue-200'"
            >
              {{ isInCollection(collection) ? 'Added' : adding ? 'Adding...' : 'Add' }}
            </button>
          </div>
        </div>
        
        <div v-else class="text-center py-8 text-gray-500">
          <FolderIcon class="mx-auto h-12 w-12 text-gray-400" />
          <p class="mt-2 text-sm">No collections yet</p>
        </div>
      </div>

      <!-- Create New Collection -->
      <div class="border-t border-gray-200 pt-4">
        <button
          @click="showCreateForm = !showCreateForm"
          class="flex items-center text-sm text-blue-600 hover:text-blue-500"
        >
          <PlusIcon class="h-4 w-4 mr-1" />
          Create New Collection
        </button>
        
        <form v-if="showCreateForm" @submit.prevent="createCollection" class="mt-4 space-y-3">
          <input
            v-model="newCollection.title"
            type="text"
            placeholder="Collection title"
            required
            class="w-full border-gray-300 rounded-md focus:border-blue-500 focus:ring-blue-500"
          />
          <textarea
            v-model="newCollection.description"
            rows="2"
            placeholder="Description (optional)"
            class="w-full border-gray-300 rounded-md focus:border-blue-500 focus:ring-blue-500"
          ></textarea>
          
          <div class="flex justify-between items-center">
            <select
              v-model="newCollection.privacy"
              class="border-gray-300 rounded-md focus:border-blue-500 focus:ring-blue-500"
            >
              <option value="public">Public</option>
              <option value="unlisted">Unlisted</option>
              <option value="private">Private</option>
            </select>
            
            <div class="flex space-x-2">
              <button
                type="button"
                @click="showCreateForm = false"
                class="px-3 py-1 text-sm text-gray-600 hover:text-gray-800"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="!newCollection.title.trim() || creating"
                class="px-4 py-1 text-sm bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50"
              >
                {{ creating ? 'Creating...' : 'Create & Add' }}
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </Modal>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { XMarkIcon, FolderIcon, PlusIcon } from '@heroicons/vue/24/outline'
import Modal from '@/Components/Modal.vue'

const props = defineProps({
  show: Boolean,
  image: Object,
})

const emit = defineEmits(['close', 'added'])

const collections = ref([])
const loading = ref(false)
const adding = ref(false)
const creating = ref(false)
const showCreateForm = ref(false)

const newCollection = reactive({
  title: '',
  description: '',
  privacy: 'public',
})

onMounted(() => {
  if (props.show) {
    loadCollections()
  }
})

watch(() => props.show, (show) => {
  if (show) {
    loadCollections()
  }
})

const loadCollections = async () => {
  try {
    loading.value = true
    const response = await axios.get(route('collections.index'), {
      params: { curator: 'mine', per_page: 50 }
    })
    collections.value = response.data.data || response.data
  } catch (error) {
    console.error('Failed to load collections:', error)
  } finally {
    loading.value = false
  }
}

const isInCollection = (collection) => {
  return collection.items?.some(item => 
    item.collectable_type === 'App\\Models\\Image' && 
    item.collectable_id === props.image.id
  )
}

const addToCollection = async (collection) => {
  if (isInCollection(collection) || adding.value) return
  
  try {
    adding.value = true
    await axios.post(route('collections.add-image', collection.id), {
      image_id: props.image.id
    })
    
    // Update local state
    if (!collection.items) collection.items = []
    collection.items.push({
      collectable_type: 'App\\Models\\Image',
      collectable_id: props.image.id,
    })
    collection.item_count = (collection.item_count || 0) + 1
    
    emit('added', collection)
  } catch (error) {
    console.error('Failed to add to collection:', error)
  } finally {
    adding.value = false
  }
}

const createCollection = async () => {
  if (!newCollection.title.trim() || creating.value) return
  
  try {
    creating.value = true
    const response = await axios.post(route('collections.store'), newCollection)
    const collection = response.data.collection || response.data
    
    // Add to local collections
    collections.value.unshift(collection)
    
    // Add image to new collection
    await addToCollection(collection)
    
    // Reset form
    Object.assign(newCollection, { title: '', description: '', privacy: 'public' })
    showCreateForm.value = false
    
  } catch (error) {
    console.error('Failed to create collection:', error)
  } finally {
    creating.value = false
  }
}
</script>
