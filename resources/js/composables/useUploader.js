import { ref, reactive, computed } from 'vue'
import api from '@/Utils/api'

export function useUploader(options = {}) {
  const {
    endpoint = '/api/v1/upload',
    maxFileSize = 10 * 1024 * 1024, // 10MB
    allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
    maxFiles = 10,
    autoUpload = false,
    preserveExif = false,
    generateThumbnails = true
  } = options

  // Reactive state
  const files = ref([])
  const uploading = ref(false)
  const uploadProgress = ref(0)
  const errors = ref([])
  const completed = ref([])
  const sessionId = ref(null)

  // Computed properties
  const totalFiles = computed(() => files.value.length)
  const uploadedCount = computed(() => completed.value.length)
  const errorCount = computed(() => errors.value.length)
  const hasErrors = computed(() => errors.value.length > 0)
  const canUpload = computed(() => files.value.length > 0 && !uploading.value)
  const isComplete = computed(() => uploadedCount.value === totalFiles.value && totalFiles.value > 0)

  // File management
  const addFiles = (fileList) => {
    const newFiles = Array.from(fileList).map((file, index) => ({
      id: `${Date.now()}-${index}`,
      file,
      name: file.name,
      size: file.size,
      type: file.type,
      preview: null,
      progress: 0,
      status: 'pending', // pending, uploading, completed, error
      error: null,
      metadata: {
        title: file.name.replace(/\.[^/.]+$/, ''),
        alt_text: '',
        description: '',
        tags: [],
        privacy: 'private'
      },
      uploadedData: null
    }))

    // Validate files
    const validFiles = []
    newFiles.forEach(fileItem => {
      const validation = validateFile(fileItem.file)
      if (validation.valid) {
        validFiles.push(fileItem)
        generatePreview(fileItem)
      } else {
        errors.value.push({
          id: fileItem.id,
          file: fileItem.name,
          message: validation.error
        })
      }
    })

    // Check total file limit
    if (files.value.length + validFiles.length > maxFiles) {
      const allowedCount = maxFiles - files.value.length
      if (allowedCount > 0) {
        files.value.push(...validFiles.slice(0, allowedCount))
        errors.value.push({
          id: 'limit-exceeded',
          message: `Only ${allowedCount} more files allowed. ${validFiles.length - allowedCount} files were rejected.`
        })
      } else {
        errors.value.push({
          id: 'limit-exceeded',
          message: `Maximum ${maxFiles} files allowed. All new files were rejected.`
        })
      }
    } else {
      files.value.push(...validFiles)
    }

    if (autoUpload && validFiles.length > 0) {
      startUpload()
    }
  }

  // File validation
  const validateFile = (file) => {
    if (!allowedTypes.includes(file.type)) {
      return {
        valid: false,
        error: `File type ${file.type} is not allowed. Allowed types: ${allowedTypes.join(', ')}`
      }
    }

    if (file.size > maxFileSize) {
      return {
        valid: false,
        error: `File size ${formatFileSize(file.size)} exceeds maximum allowed size ${formatFileSize(maxFileSize)}`
      }
    }

    return { valid: true }
  }

  // Preview generation
  const generatePreview = (fileItem) => {
    if (fileItem.file.type.startsWith('image/')) {
      const reader = new FileReader()
      reader.onload = (e) => {
        fileItem.preview = e.target.result
      }
      reader.readAsDataURL(fileItem.file)
    }
  }

  // Remove file
  const removeFile = (fileId) => {
    const index = files.value.findIndex(f => f.id === fileId)
    if (index !== -1) {
      files.value.splice(index, 1)
    }
    
    // Remove related errors
    errors.value = errors.value.filter(e => e.id !== fileId)
  }

  // Clear all files
  const clearFiles = () => {
    files.value = []
    errors.value = []
    completed.value = []
    uploadProgress.value = 0
    sessionId.value = null
  }

  // Update file metadata
  const updateFileMetadata = (fileId, metadata) => {
    const fileItem = files.value.find(f => f.id === fileId)
    if (fileItem) {
      Object.assign(fileItem.metadata, metadata)
    }
  }

  // Bulk update metadata
  const updateAllMetadata = (metadata) => {
    files.value.forEach(fileItem => {
      Object.assign(fileItem.metadata, metadata)
    })
  }

  // Upload functions
  const startUpload = async () => {
    if (uploading.value || files.value.length === 0) return

    uploading.value = true
    uploadProgress.value = 0
    errors.value = errors.value.filter(e => e.id === 'limit-exceeded') // Keep limit errors
    
    try {
      // Create upload session
      sessionId.value = await createUploadSession()

      // Upload files sequentially or in parallel
      await uploadFiles()

      // Complete the session
      await completeUploadSession()

    } catch (error) {
      console.error('Upload failed:', error)
      errors.value.push({
        id: 'upload-session',
        message: error.message || 'Upload session failed'
      })
    } finally {
      uploading.value = false
    }
  }

  const createUploadSession = async () => {
    const response = await api.post(`${endpoint}/session`, {
      file_count: files.value.length,
      total_size: files.value.reduce((sum, f) => sum + f.file.size, 0)
    })
    
    return response.data.session_id
  }

  const uploadFiles = async () => {
    const totalFiles = files.value.length
    let completedFiles = 0

    for (const fileItem of files.value) {
      if (fileItem.status === 'completed') {
        completedFiles++
        continue
      }

      try {
        fileItem.status = 'uploading'
        await uploadSingleFile(fileItem)
        fileItem.status = 'completed'
        completed.value.push(fileItem.id)
        completedFiles++
        
        uploadProgress.value = Math.round((completedFiles / totalFiles) * 100)
        
      } catch (error) {
        fileItem.status = 'error'
        fileItem.error = error.message
        errors.value.push({
          id: fileItem.id,
          file: fileItem.name,
          message: error.message
        })
      }
    }
  }

  const uploadSingleFile = async (fileItem) => {
    // Step 1: Get presigned URL
    const presignResponse = await api.post(`${endpoint}/presign`, {
      filename: fileItem.file.name,
      content_type: fileItem.file.type,
      size: fileItem.file.size,
      session_id: sessionId.value
    })

    const { upload_url, fields, image_id } = presignResponse.data

    // Step 2: Upload to storage (S3/MinIO)
    const formData = new FormData()
    Object.entries(fields || {}).forEach(([key, value]) => {
      formData.append(key, value)
    })
    formData.append('file', fileItem.file)

    await fetch(upload_url, {
      method: 'POST',
      body: formData
    })

    // Step 3: Complete upload and save metadata
    const completeResponse = await api.post(`${endpoint}/complete`, {
      image_id,
      session_id: sessionId.value,
      metadata: {
        ...fileItem.metadata,
        original_filename: fileItem.file.name,
        preserve_exif: preserveExif,
        generate_thumbnails: generateThumbnails
      }
    })

    fileItem.uploadedData = completeResponse.data
    return completeResponse.data
  }

  const completeUploadSession = async () => {
    if (!sessionId.value) return

    await api.post(`${endpoint}/session/${sessionId.value}/complete`, {
      uploaded_files: completed.value.length,
      failed_files: errorCount.value
    })
  }

  // Retry failed uploads
  const retryFile = async (fileId) => {
    const fileItem = files.value.find(f => f.id === fileId)
    if (!fileItem || fileItem.status === 'uploading') return

    fileItem.status = 'pending'
    fileItem.error = null
    fileItem.progress = 0

    // Remove from errors
    errors.value = errors.value.filter(e => e.id !== fileId)

    try {
      fileItem.status = 'uploading'
      await uploadSingleFile(fileItem)
      fileItem.status = 'completed'
      completed.value.push(fileId)
    } catch (error) {
      fileItem.status = 'error'
      fileItem.error = error.message
      errors.value.push({
        id: fileId,
        file: fileItem.name,
        message: error.message
      })
    }
  }

  const retryAllFailed = async () => {
    const failedFiles = files.value.filter(f => f.status === 'error')
    
    for (const fileItem of failedFiles) {
      await retryFile(fileItem.id)
    }
  }

  // Cancel upload
  const cancelUpload = () => {
    if (!uploading.value) return

    uploading.value = false
    
    // Reset pending files
    files.value.forEach(fileItem => {
      if (fileItem.status === 'uploading' || fileItem.status === 'pending') {
        fileItem.status = 'pending'
        fileItem.progress = 0
      }
    })

    uploadProgress.value = 0
  }

  // Direct upload (without presign flow)
  const directUpload = async (fileItem) => {
    const formData = new FormData()
    formData.append('file', fileItem.file)
    formData.append('metadata', JSON.stringify(fileItem.metadata))
    
    const response = await api.post(`${endpoint}/direct`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      },
      onUploadProgress: (progressEvent) => {
        fileItem.progress = Math.round(
          (progressEvent.loaded * 100) / progressEvent.total
        )
      }
    })

    return response.data
  }

  // Utility functions
  const formatFileSize = (bytes) => {
    if (bytes === 0) return '0 Bytes'
    const k = 1024
    const sizes = ['Bytes', 'KB', 'MB', 'GB']
    const i = Math.floor(Math.log(bytes) / Math.log(k))
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
  }

  const getFileIcon = (type) => {
    if (type.startsWith('image/')) return 'photo'
    if (type.startsWith('video/')) return 'videocamera'
    return 'document'
  }

  // URL upload
  const addFromUrl = async (url, metadata = {}) => {
    try {
      const response = await api.post(`${endpoint}/from-url`, {
        url,
        metadata: {
          title: metadata.title || 'Image from URL',
          alt_text: metadata.alt_text || '',
          description: metadata.description || '',
          tags: metadata.tags || [],
          privacy: metadata.privacy || 'private'
        }
      })

      const fileItem = {
        id: `url-${Date.now()}`,
        file: null,
        name: response.data.filename || 'Image from URL',
        size: response.data.size || 0,
        type: response.data.content_type || 'image/jpeg',
        preview: response.data.thumbnail || url,
        progress: 100,
        status: 'completed',
        error: null,
        metadata: response.data.metadata,
        uploadedData: response.data
      }

      files.value.push(fileItem)
      completed.value.push(fileItem.id)
      
      return response.data
    } catch (error) {
      errors.value.push({
        id: 'url-upload',
        message: `Failed to upload from URL: ${error.response?.data?.message || error.message}`
      })
      throw error
    }
  }

  // Paste from clipboard
  const addFromClipboard = async () => {
    try {
      const clipboardItems = await navigator.clipboard.read()
      
      for (const clipboardItem of clipboardItems) {
        for (const type of clipboardItem.types) {
          if (type.startsWith('image/')) {
            const blob = await clipboardItem.getType(type)
            const file = new File([blob], `clipboard-${Date.now()}.${type.split('/')[1]}`, { type })
            addFiles([file])
            break
          }
        }
      }
    } catch (error) {
      errors.value.push({
        id: 'clipboard',
        message: `Failed to paste from clipboard: ${error.message}`
      })
    }
  }

  return {
    // State
    files,
    uploading,
    uploadProgress,
    errors,
    completed,
    sessionId,

    // Computed
    totalFiles,
    uploadedCount,
    errorCount,
    hasErrors,
    canUpload,
    isComplete,

    // Methods
    addFiles,
    removeFile,
    clearFiles,
    updateFileMetadata,
    updateAllMetadata,
    startUpload,
    retryFile,
    retryAllFailed,
    cancelUpload,
    directUpload,
    addFromUrl,
    addFromClipboard,

    // Utilities
    formatFileSize,
    getFileIcon,
    validateFile
  }
}