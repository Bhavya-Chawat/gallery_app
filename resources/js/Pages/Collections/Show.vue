<template>
  <AuthenticatedLayout>
    <Head :title="collection.title" />

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Collection Header -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
          <div class="flex justify-between items-start">
            <div class="flex-1">
              <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ collection.title }}</h1>
              <p v-if="collection.description" class="text-gray-600 mb-4">{{ collection.description }}</p>
              
              <div class="flex items-center space-x-6 text-sm text-gray-500">
                <span>by {{ collection.curator.name }}</span>
                <span>{{ collection.item_count }} items</span>
                <span>{{ formatDate(collection.created_at) }}</span>
              </div>
            </div>
            
            <div v-if="canEdit" class="ml-4">
              <button
                @click="showAddModal = true"
                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700"
              >
                <PlusIcon class="h-4 w-4 mr-2" />
                Add Images
              </button>
            </div>
          </div>
        </div>

        <!-- Collection Items Grid -->
        <div v-if="collection.items.length > 0" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
          <div
            v-for="item in collection.items"
            :key="item.id"
            class="group relative aspect-square bg-gray-200 rounded-lg overflow-hidden"
          >
            <Link :href="route('images.show', item.collectable.slug)">
              <img
                :src="item.collectable.thumbnail_url"
                :alt="item.collectable.alt_text || item.collectable.title"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200"
              />
            </Link>
            
            <!-- Remove Button (for collection owner) -->
            <button
              v-if="canEdit"
              @click="removeFromCollection(item.collectable.id)"
              class="absolute top-2 right-2 p-1 bg-red-500 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity"
            >
              <XMarkIcon class="h-4 w-4" />
            </button>

            <!-- Image Info -->
            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-3">
              <h3 class="text-white text-sm font-medium truncate">{{ item.collectable.title }}</h3>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-12">
          <FolderIcon class="mx-auto h-12 w-12 text-gray-400" />
          <h3 class="mt-2 text-sm font-medium text-gray-900">No images in this collection</h3>
          <p class="mt-1 text-sm text-gray-500">Get started by adding some images.</p>
        </div>
      </div>
    </div>

    <!-- Add Images Modal -->
    <Modal :show="showAddModal" @close="showAddModal = false">
      <div class="p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Add Images to Collection</h3>
        <!-- Add your image selection component here -->
        <div class="flex justify-end space-x-3 mt-6">
          <button
            @click="showAddModal = false"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
          >
            Cancel
          </button>
          <button
            @click="addSelectedImages"
            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700"
          >
            Add to Collection
          </button>
        </div>
      </div>
    </Modal>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { PlusIcon, XMarkIcon, FolderIcon } from '@heroicons/vue/24/outline'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Modal from '@/Components/Modal.vue'

const props = defineProps({
  collection: Object,
  canEdit: Boolean,
})

const showAddModal = ref(false)

const removeFromCollection = (imageId) => {
  if (confirm('Remove this image from the collection?')) {
    router.delete(route('collections.remove-image', props.collection.id), {
      data: { image_id: imageId }
    })
  }
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString()
}
</script>
