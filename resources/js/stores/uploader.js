import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { useUploader } from '@/composables/useUploader'

export const useUploaderStore = defineStore('uploader', () => {
  // Upload instances
  const uploaders = ref(new Map())
  const globalSettings = ref({
    maxFileSize: 10 * 1024 * 1024, // 10MB
    allowedTypes: ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
    maxFiles: 50,
    autoUpload: false,
    preserveExif: false,
    generateThumbnails: true,
    defaultPrivacy: 'private',
    defaultAlbum: null
  })

  // Global state
  const isUploading = ref(false)
  const totalProgress = ref(0)
  const notifications = ref([])

  // Computed
  const totalUploaders = computed(() => uploaders.value.size)
  const hasActiveUploads = computed(() => 
    Array.from(uploaders.value.values()).some(uploader => uploader.uploading.value)
  )
  const globalStats = computed(() => {
    const stats = {
      totalFiles: 0,
      uploadedFiles: 0,
      failedFiles: 0,
      pendingFiles: 0
    }

    uploaders.value.forEach(uploader => {
      stats.totalFiles += uploader.totalFiles.value
      stats.uploadedFiles += uploader.uploadedCount.value
      stats.failedFiles += uploader.errorCount.value
      stats.pendingFiles += uploader.totalFiles.value - uploader.uploadedCount.value - uploader.errorCount.value
    })

    return stats
  })

  // Create uploader instance
  const createUploader = (id, options = {}) => {
    const uploaderOptions = {
      ...globalSettings.value,
      ...options
    }

    const uploader = useUploader(uploaderOptions)
    uploaders.value.set(id, uploader)

    // Listen to uploader events
    watchUploaderProgress(id, uploader)

    return uploader
  }

  // Get uploader instance
  const getUploader = (id) => {
    return uploaders.value.get(id)
  }

  // Remove uploader instance
  const removeUploader = (id) => {
    return uploaders.value.delete(id)
  }

  // Watch uploader progress
  const watchUploaderProgress = (id, uploader) => {
    // Watch for upload completion
    uploader.isComplete.value && (() => {
      addNotification({
        type: 'success',
        title: 'Upload Complete',
        message: `All files uploaded successfully for ${id}`,
        duration: 5000
      })
    })

    // Watch for errors
    uploader.hasErrors.value && (() => {
      addNotification({
        type: 'error',
        title: 'Upload Errors',
        message: `Some files failed to upload in ${id}`,
        duration: 0 // Don't auto-dismiss errors
      })
    })
  }

  // Global upload management
  const startAllUploads = async () => {
    isUploading.value = true
    const uploadPromises = []

    uploaders.value.forEach((uploader, id) => {
      if (uploader.canUpload.value) {
        uploadPromises.push(uploader.startUpload())
      }
    })

    try {
      await Promise.allSettled(uploadPromises)
      
      addNotification({
        type: 'success',
        title: 'Bulk Upload Complete',
        message: 'All upload queues have been processed',
        duration: 5000
      })
    } catch (error) {
      addNotification({
        type: 'error',
        title: 'Bulk Upload Failed',
        message: 'Some uploads failed to complete',
        duration: 0
      })
    } finally {
      isUploading.value = false
    }
  }

  // Notification management
  const addNotification = (notification) => {
    notifications.value.push(notification)
  }

  return {
    uploaders,
    globalSettings,
    isUploading,
    totalProgress,
    notifications,
    totalUploaders,
    hasActiveUploads,
    globalStats,
    createUploader,
    getUploader,
    removeUploader,
    startAllUploads
  }
})