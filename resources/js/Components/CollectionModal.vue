<template>
  <Modal :show="show" @close="$emit('close')" max-width="lg">
    <div class="relative overflow-hidden bg-gradient-to-br from-slate-800/95 via-slate-700/95 to-slate-800/95 backdrop-blur-2xl rounded-2xl border border-white/10" style="box-shadow: 0 25px 50px -12px rgba(139, 92, 246, 0.25);">
      
      <!-- Animated Mesh Background -->
      <div class="absolute inset-0 opacity-10 pointer-events-none">
        <svg class="w-full h-full animate-mesh-shift" viewBox="0 0 100 100" preserveAspectRatio="none">
          <defs>
            <linearGradient id="meshGradModal" x1="0%" y1="0%" x2="100%" y2="100%" gradientUnits="objectBoundingBox">
              <stop offset="0%" style="stop-color:#8b5cf6;stop-opacity:0.4">
                <animate attributeName="stop-color" values="#8b5cf6;#06b6d4;#a855f7;#8b5cf6" dur="8s" repeatCount="indefinite"/>
              </stop>
              <stop offset="50%" style="stop-color:#06b6d4;stop-opacity:0.3">
                <animate attributeName="stop-color" values="#06b6d4;#a855f7;#8b5cf6;#06b6d4" dur="8s" repeatCount="indefinite"/>
              </stop>
              <stop offset="100%" style="stop-color:#a855f7;stop-opacity:0.4">
                <animate attributeName="stop-color" values="#a855f7;#8b5cf6;#06b6d4;#a855f7" dur="8s" repeatCount="indefinite"/>
              </stop>
            </linearGradient>
          </defs>
          <rect width="100%" height="100%" fill="url(#meshGradModal)"/>
          <path d="M0,30 Q50,10 100,30 L100,70 Q50,90 0,70 Z" fill="url(#meshGradModal)" opacity="0.3">
            <animateTransform attributeName="transform" type="translate" values="0,0; 5,3; 0,0" dur="12s" repeatCount="indefinite"/>
          </path>
        </svg>
      </div>

      <!-- Floating Particles -->
      <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div v-for="i in 15" :key="i" 
             class="absolute w-1 h-1 rounded-full animate-float-particle"
             :style="{
               left: Math.random() * 100 + '%',
               top: Math.random() * 100 + '%',
               background: ['#8b5cf6', '#06b6d4', '#a855f7'][Math.floor(Math.random() * 3)],
               animationDelay: Math.random() * 8 + 's',
               animationDuration: (8 + Math.random() * 4) + 's'
             }">
        </div>
      </div>

      <div class="relative z-10 p-8">
        <!-- Enhanced Header -->
        <div class="flex items-center justify-between mb-8 animate-fade-in-up">
          <h3 class="text-2xl font-bold bg-gradient-to-r from-slate-200 via-white to-slate-200 bg-clip-text text-transparent animate-text-shimmer">
            Add to Collection
          </h3>
          <button 
            @click="$emit('close')" 
            class="p-2 rounded-xl bg-white/10 hover:bg-red-500/20 backdrop-blur-xl border border-white/10 hover:border-red-400/30 text-slate-300 hover:text-red-300 transition-all duration-300 hover:scale-110 group"
          >
            <XMarkIcon class="h-6 w-6" />
          </button>
        </div>

        <!-- Enhanced My Collections Section -->
        <div class="mb-8 animate-fade-in-up animation-delay-200">
          <h4 class="text-base font-semibold bg-gradient-to-r from-violet-300 to-cyan-300 bg-clip-text text-transparent mb-4">
            My Collections
          </h4>
          
          <!-- Loading State -->
          <div v-if="loading" class="text-center py-8">
            <div class="relative mx-auto w-12 h-12">
              <div class="w-12 h-12 border-4 border-violet-500/30 border-t-violet-500 rounded-full animate-spin"></div>
              <div class="absolute inset-0 w-12 h-12 border-4 border-transparent border-t-cyan-500 rounded-full animate-spin animation-delay-150"></div>
            </div>
            <p class="mt-4 text-slate-400">Loading collections...</p>
          </div>
          
          <!-- Collections List -->
          <div v-else-if="collections.length > 0" class="space-y-3 max-h-80 overflow-y-auto scrollbar-thin scrollbar-thumb-violet-500/30 scrollbar-track-slate-700/30">
            <div
              v-for="(collection, index) in collections"
              :key="collection.id"
              class="flex items-center justify-between p-4 bg-white/5 backdrop-blur-xl border border-white/10 rounded-xl hover:bg-white/10 hover:border-violet-400/30 hover:shadow-lg hover:shadow-violet-500/10 transition-all duration-300 group animate-fade-in-up"
              :style="{ animationDelay: (index * 0.1) + 's' }"
            >
              <div class="flex items-center space-x-4">
                <div class="relative group-hover:scale-110 transition-transform duration-300">
                  <img
                    v-if="collection.cover_image"
                    :src="collection.cover_image.thumbnail_url"
                    :alt="collection.title"
                    class="w-12 h-12 object-cover rounded-xl shadow-lg border border-white/20"
                  />
                  <div v-else class="w-12 h-12 bg-gradient-to-br from-violet-500/20 via-purple-500/20 to-cyan-500/20 backdrop-blur-xl rounded-xl border border-white/20 flex items-center justify-center shadow-lg">
                    <FolderIcon class="w-6 h-6 text-slate-300" />
                  </div>
                </div>
                
                <div>
                  <h5 class="text-base font-medium text-slate-200 group-hover:text-white transition-colors">
                    {{ collection.title }}
                  </h5>
                  <p class="text-sm text-slate-400 group-hover:text-slate-300 transition-colors">
                    {{ collection.item_count }} items
                  </p>
                </div>
              </div>
              
              <button
                @click="addToCollection(collection)"
                :disabled="isInCollection(collection) || adding"
                class="px-4 py-2 text-sm font-medium rounded-xl transition-all duration-300 hover:scale-105 disabled:hover:scale-100 shadow-lg"
                :class="isInCollection(collection) 
                  ? 'bg-gradient-to-r from-green-500/20 to-emerald-500/20 text-green-300 border border-green-400/30 cursor-default' 
                  : 'bg-gradient-to-r from-violet-500/20 via-purple-500/20 to-cyan-500/20 text-slate-200 border border-violet-400/30 hover:from-violet-500/30 hover:via-purple-500/30 hover:to-cyan-500/30 hover:border-violet-400/50 hover:text-white'"
              >
                {{ isInCollection(collection) ? '✓ Added' : adding ? 'Adding...' : 'Add' }}
              </button>
            </div>
          </div>
          
          <!-- Empty State -->
          <div v-else class="text-center py-12 animate-fade-in-up">
            <div class="mx-auto w-20 h-20 bg-gradient-to-br from-violet-500/10 via-purple-500/10 to-cyan-500/10 backdrop-blur-xl rounded-2xl border border-white/10 flex items-center justify-center mb-4 shadow-2xl">
              <FolderIcon class="h-10 w-10 text-slate-400" />
            </div>
            <p class="text-slate-400 font-medium">No collections yet</p>
            <p class="text-sm text-slate-500 mt-1">Create your first collection below</p>
          </div>
        </div>

        <!-- Enhanced Create New Collection -->
        <div class="border-t border-white/10 pt-6 animate-fade-in-up animation-delay-400">
          <button
            @click="showCreateForm = !showCreateForm"
            class="flex items-center text-base font-medium bg-gradient-to-r from-violet-400 to-cyan-400 bg-clip-text text-transparent hover:from-violet-300 hover:to-cyan-300 transition-all duration-300 group"
          >
            <div class="p-1 rounded-lg bg-gradient-to-r from-violet-500/20 to-cyan-500/20 border border-violet-400/30 mr-2 group-hover:scale-110 transition-transform duration-300">
              <PlusIcon class="h-4 w-4 text-violet-300" />
            </div>
            Create New Collection
          </button>
          
          <!-- Enhanced Create Form -->
          <div v-if="showCreateForm" class="mt-6 p-6 bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl animate-slide-down">
            <form @submit.prevent="createCollection" class="space-y-5">
              <!-- Title Input -->
              <div class="space-y-2">
                <label class="text-sm font-medium text-slate-300">Collection Title</label>
                <input
                  v-model="newCollection.title"
                  type="text"
                  placeholder="Enter collection name..."
                  required
                  class="w-full bg-white/5 backdrop-blur-xl border border-white/20 rounded-xl px-4 py-3 text-slate-200 placeholder-slate-400 focus:border-violet-400/50 focus:ring-4 focus:ring-violet-500/20 focus:outline-none transition-all duration-300"
                />
              </div>

              <!-- Description Textarea -->
              <div class="space-y-2">
                <label class="text-sm font-medium text-slate-300">Description (optional)</label>
                <textarea
                  v-model="newCollection.description"
                  rows="3"
                  placeholder="Describe your collection..."
                  class="w-full bg-white/5 backdrop-blur-xl border border-white/20 rounded-xl px-4 py-3 text-slate-200 placeholder-slate-400 focus:border-cyan-400/50 focus:ring-4 focus:ring-cyan-500/20 focus:outline-none transition-all duration-300 resize-none"
                ></textarea>
              </div>
              
              <!-- Privacy & Actions -->
              <div class="flex justify-between items-end gap-4">
                <div class="space-y-2">
                  <label class="text-sm font-medium text-slate-300">Privacy</label>
                  <select
                    v-model="newCollection.privacy"
                    class="bg-white/5 backdrop-blur-xl border border-white/20 rounded-xl px-4 py-3 text-slate-200 focus:border-purple-400/50 focus:ring-4 focus:ring-purple-500/20 focus:outline-none transition-all duration-300"
                  >
                    <option value="public" class="bg-slate-800 text-slate-200">Public</option>
                    <option value="unlisted" class="bg-slate-800 text-slate-200">Unlisted</option>
                    <option value="private" class="bg-slate-800 text-slate-200">Private</option>
                  </select>
                </div>
                
                <div class="flex space-x-3">
                  <button
                    type="button"
                    @click="showCreateForm = false"
                    class="px-5 py-3 text-sm font-medium text-slate-400 hover:text-slate-200 bg-white/5 hover:bg-white/10 border border-white/20 hover:border-white/30 rounded-xl transition-all duration-300 hover:scale-105"
                  >
                    Cancel
                  </button>
                  <button
                    type="submit"
                    :disabled="!newCollection.title.trim() || creating"
                    class="px-6 py-3 text-sm font-medium bg-gradient-to-r from-violet-500 via-purple-500 to-cyan-500 text-white rounded-xl hover:from-violet-600 hover:via-purple-600 hover:to-cyan-600 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300 hover:scale-105 shadow-lg hover:shadow-violet-500/25"
                  >
                    {{ creating ? 'Creating...' : 'Create & Add' }}
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </Modal>
</template>

<script setup>
import { ref, reactive, onMounted, watch } from 'vue'
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

<style scoped>
@keyframes mesh-shift {
  0%, 100% { transform: translate(0, 0) rotate(0deg); }
  33% { transform: translate(20px, -20px) rotate(1deg); }
  66% { transform: translate(-15px, 15px) rotate(-1deg); }
}

@keyframes float-particle {
  0%, 100% { 
    transform: translateY(0px) rotate(0deg);
    opacity: 0.3;
  }
  50% { 
    transform: translateY(-20px) rotate(180deg);
    opacity: 0.7;
  }
}

@keyframes text-shimmer {
  0% { background-position: -200% center; }
  100% { background-position: 200% center; }
}

@keyframes fade-in-up {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes slide-down {
  from {
    opacity: 0;
    transform: translateY(-20px) scale(0.95);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

.animate-mesh-shift {
  animation: mesh-shift 15s ease-in-out infinite;
}

.animate-float-particle {
  animation: float-particle 8s ease-in-out infinite;
}

.animate-text-shimmer {
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
  background-size: 200% 100%;
  animation: text-shimmer 3s ease-in-out infinite;
}

.animate-fade-in-up {
  animation: fade-in-up 0.6s ease-out forwards;
  opacity: 0;
}

.animate-slide-down {
  animation: slide-down 0.5s ease-out;
}

.animation-delay-150 {
  animation-delay: 0.15s;
}

.animation-delay-200 {
  animation-delay: 0.2s;
}

.animation-delay-400 {
  animation-delay: 0.4s;
}

/* Custom scrollbar */
.scrollbar-thin {
  scrollbar-width: thin;
}

.scrollbar-thumb-violet-500\/30 {
  scrollbar-color: rgba(139, 92, 246, 0.3) transparent;
}

.scrollbar-track-slate-700\/30 {
  scrollbar-color: rgba(51, 65, 85, 0.3) transparent;
}

/* Webkit scrollbar styles */
.scrollbar-thin::-webkit-scrollbar {
  width: 6px;
}

.scrollbar-thin::-webkit-scrollbar-track {
  background: rgba(51, 65, 85, 0.3);
  border-radius: 10px;
}

.scrollbar-thin::-webkit-scrollbar-thumb {
  background: rgba(139, 92, 246, 0.3);
  border-radius: 10px;
}

.scrollbar-thin::-webkit-scrollbar-thumb:hover {
  background: rgba(139, 92, 246, 0.5);
}
</style>