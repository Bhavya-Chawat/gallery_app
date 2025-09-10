<template>
  <AppLayout>
    <Head title="Upload Images" />

    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Upload Images
          </h2>
          <p class="text-sm text-gray-600 mt-1">
            Share your photos with the world
          </p>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Storage Usage Warning -->
        <div v-if="storageUsage.percentage > 90" class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
          <div class="flex">
            <ExclamationTriangleIcon class="h-5 w-5 text-red-400" />
            <div class="ml-3">
              <h3 class="text-sm font-medium text-red-800">
                Storage quota almost full
              </h3>
              <p class="mt-1 text-sm text-red-700">
                You've used {{ storageUsage.percentage.toFixed(1) }}% of your storage quota. 
                Consider deleting some images or contact support to increase your quota.
              </p>
            </div>
          </div>
        </div>

        <!-- Upload Component -->
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
          <div class="p-6">
            <UploadDropzone
              :max-files="10"
              :max-size="maxUploadSize"
              :accepted-formats="allowedMimes"
              :albums="albums"
              :default-privacy="defaultPrivacy"
              :storage-remaining="storageUsage.remaining"
              @upload-complete="handleUploadComplete"
              @upload-error="handleUploadError"
            />
          </div>
        </div>

        <!-- Upload Guidelines -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-md p-4">
          <div class="flex">
            <InformationCircleIcon class="h-5 w-5 text-blue-400" />
            <div class="ml-3">
              <h3 class="text-sm font-medium text-blue-800">
                Upload Guidelines
              </h3>
              <div class="mt-2 text-sm text-blue-700">
                <ul class="list-disc list-inside space-y-1">
                  <li>Maximum file size: {{ formatBytes(maxUploadSize) }}</li>
                  <li>Supported formats: {{ allowedMimes.split(',').map(m => m.toUpperCase()).join(', ') }}</li>
                  <li>You can upload up to 10 images at once</li>
                  <li>Images will be automatically resized and optimized</li>
                  <li>EXIF data will be preserved but GPS coordinates will be removed for privacy</li>
                </ul>
              </div>
            </div>
          </div>
        </div>

        <!-- Storage Information -->
        <div class="mt-6 bg-white shadow-sm rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Storage Usage</h3>
          <StorageUsageCard
            :used="storageUsage.used"
            :quota="storageUsage.quota"
            :percentage="storageUsage.percentage"
            :show-details="true"
          />
        </div>

        <!-- Recent Uploads -->
        <div v-if="recentUploads.length > 0" class="mt-6 bg-white shadow-sm rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Uploads</h3>
          <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <Link
              v-for="image in recentUploads"
              :key="image.id"
              :href="route('images.show', image.slug)"
              class="aspect-square bg-gray-100 rounded-lg overflow-hidden hover:shadow-md transition-shadow"
            >
              <img
                :src="getImageUrl(image, 'small')"
                :alt="image.alt_text || image.title"
                class="w-full h-full object-cover hover:scale-105 transition-transform"
              >
            </Link>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import {
  ExclamationTriangleIcon,
  InformationCircleIcon,
} from '@heroicons/vue/24/outline'

import AppLayout from '@/Layouts/AppLayout.vue'
import UploadDropzone from '@/Components/Gallery/UploadDropzone.vue'
import StorageUsageCard from '@/Components/StorageUsageCard.vue'

const props = defineProps({
  albums: {
    type: Array,
    default: () => [],
  },
  maxUploadSize: {
    type: Number,
    default: 52428800, // 50MB
  },
  allowedMimes: {
    type: String,
    default: 'jpg,jpeg,png,webp,avif',
  },
  defaultPrivacy: {
    type: String,
    default: 'unlisted',
  },
  storageUsage: {
    type: Object,
    required: true,
  },
  recentUploads: {
    type: Array,
    default: () => [],
  },
})

const formatBytes = (bytes) => {
  if (bytes === 0) return '0 B'
  const k = 1024
  const sizes = ['B', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const getImageUrl = (image, variant = 'small') => {
  if (image.versions) {
    const version = image.versions.find(v => v.variant === variant)
    if (version) return version.url
  }
  return image.url || image.storage_path
}

const handleUploadComplete = (data) => {
  // Redirect to the first uploaded image or gallery
  if (data.uploaded && data.uploaded.length > 0) {
    const firstImage = data.uploaded[0]
    router.visit(route('images.show', firstImage.image_id))
  } else {
    router.visit(route('my.images'))
  }
}

const handleUploadError = (error) => {
  console.error('Upload error:', error)
  // Error handling is done by the UploadDropzone component
}
</script>
