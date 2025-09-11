import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/Utils/api'

export const useGalleryStore = defineStore('gallery', () => {
  // State
  const images = ref([])
  const albums = ref([])
  const collections = ref([])
  const selectedImages = ref(new Set())
  const currentAlbum = ref(null)
  const currentCollection = ref(null)
  const loading = ref(false)
  const error = ref(null)
  const pagination = ref({
    current_page: 1,
    last_page: 1,
    per_page: 20,
    total: 0
  })

  // View state
  const viewMode = ref('grid') // grid, masonry, list
  const sortBy = ref('created_at') // created_at, title, size, views
  const sortOrder = ref('desc') // asc, desc
  const filters = ref({
    privacy: null,
    tags: [],
    album_id: null,
    date_from: null,
    date_to: null,
    camera: null,
    orientation: null
  })

  // Lightbox state
  const lightboxOpen = ref(false)
  const lightboxIndex = ref(0)
  const lightboxImages = ref([])

  // Computed properties
  const selectedCount = computed(() => selectedImages.value.size)
  const hasSelection = computed(() => selectedImages.value.size > 0)
  const isAllSelected = computed(() => 
    images.value.length > 0 && selectedImages.value.size === images.value.length
  )
  const activeFilters = computed(() => 
    Object.entries(filters.value).filter(([key, value]) => 
      value !== null && value !== '' && 
      (Array.isArray(value) ? value.length > 0 : true)
    )
  )

  // Image management
  const fetchImages = async (params = {}) => {
    loading.value = true
    error.value = null

    try {
      const response = await api.get('/api/v1/images', {
        params: {
          page: pagination.value.current_page,
          per_page: pagination.value.per_page,
          sort: sortBy.value,
          order: sortOrder.value,
          ...filters.value,
          ...params
        }
      })

      images.value = response.data.data
      pagination.value = {
        current_page: response.data.current_page,
        last_page: response.data.last_page,
        per_page: response.data.per_page,
        total: response.data.total
      }
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch images'
      console.error('Failed to fetch images:', err)
    } finally {
      loading.value = false
    }
  }

  const fetchMyImages = async (params = {}) => {
    loading.value = true
    error.value = null

    try {
      const response = await api.get('/api/v1/my/images', {
        params: {
          page: pagination.value.current_page,
          per_page: pagination.value.per_page,
          sort: sortBy.value,
          order: sortOrder.value,
          ...filters.value,
          ...params
        }
      })

      images.value = response.data.data
      pagination.value = response.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch your images'
      console.error('Failed to fetch user images:', err)
    } finally {
      loading.value = false
    }
  }

  const fetchImage = async (id) => {
    try {
      const response = await api.get(`/api/v1/images/${id}`)
      return response.data
    } catch (err) {
      throw new Error(err.response?.data?.message || 'Failed to fetch image')
    }
  }

  const updateImage = async (id, data) => {
    try {
      const response = await api.put(`/api/v1/images/${id}`, data)
      
      // Update in local state
      const index = images.value.findIndex(img => img.id === id)
      if (index !== -1) {
        images.value[index] = response.data
      }
      
      return response.data
    } catch (err) {
      throw new Error(err.response?.data?.message || 'Failed to update image')
    }
  }

  const deleteImage = async (id) => {
    try {
      await api.delete(`/api/v1/images/${id}`)
      
      // Remove from local state
      images.value = images.value.filter(img => img.id !== id)
      selectedImages.value.delete(id)
      
      return true
    } catch (err) {
      throw new Error(err.response?.data?.message || 'Failed to delete image')
    }
  }

  const likeImage = async (id) => {
    try {
      const response = await api.post(`/api/v1/images/${id}/like`)
      
      // Update in local state
      const image = images.value.find(img => img.id === id)
      if (image) {
        image.is_liked = response.data.liked
        image.likes_count = response.data.likes_count
      }
      
      return response.data
    } catch (err) {
      throw new Error(err.response?.data?.message || 'Failed to like image')
    }
  }

  // Album management
  const fetchAlbums = async (params = {}) => {
    try {
      const response = await api.get('/api/v1/albums', { params })
      albums.value = response.data.data || response.data
      return response.data
    } catch (err) {
      throw new Error(err.response?.data?.message || 'Failed to fetch albums')
    }
  }

  const fetchMyAlbums = async (params = {}) => {
    try {
      const response = await api.get('/api/v1/my/albums', { params })
      albums.value = response.data.data || response.data
      return response.data
    } catch (err) {
      throw new Error(err.response?.data?.message || 'Failed to fetch your albums')
    }
  }

  const fetchAlbum = async (id) => {
    try {
      const response = await api.get(`/api/v1/albums/${id}`)
      currentAlbum.value = response.data
      return response.data
    } catch (err) {
      throw new Error(err.response?.data?.message || 'Failed to fetch album')
    }
  }

  const createAlbum = async (data) => {
    try {
      const response = await api.post('/api/v1/albums', data)
      albums.value.unshift(response.data)
      return response.data
    } catch (err) {
      throw new Error(err.response?.data?.message || 'Failed to create album')
    }
  }

  const updateAlbum = async (id, data) => {
    try {
      const response = await api.put(`/api/v1/albums/${id}`, data)
      
      // Update in local state
      const index = albums.value.findIndex(album => album.id === id)
      if (index !== -1) {
        albums.value[index] = response.data
      }
      
      if (currentAlbum.value?.id === id) {
        currentAlbum.value = response.data
      }
      
      return response.data
    } catch (err) {
      throw new Error(err.response?.data?.message || 'Failed to update album')
    }
  }

  const deleteAlbum = async (id) => {
    try {
      await api.delete(`/api/v1/albums/${id}`)
      
      // Remove from local state
      albums.value = albums.value.filter(album => album.id !== id)
      if (currentAlbum.value?.id === id) {
        currentAlbum.value = null
      }
      
      return true
    } catch (err) {
      throw new Error(err.response?.data?.message || 'Failed to delete album')
    }
  }

  const addImagesToAlbum = async (albumId, imageIds) => {
    try {
      const response = await api.post(`/api/v1/albums/${albumId}/add-images`, {
        image_ids: imageIds
      })
      
      return response.data
    } catch (err) {
      throw new Error(err.response?.data?.message || 'Failed to add images to album')
    }
  }

  const removeImagesFromAlbum = async (albumId, imageIds) => {
    try {
      const response = await api.delete(`/api/v1/albums/${albumId}/remove-images`, {
        data: { image_ids: imageIds }
      })
      
      return response.data
    } catch (err) {
      throw new Error(err.response?.data?.message || 'Failed to remove images from album')
    }
  }

  const reorderAlbumImages = async (albumId, imageIds) => {
    try {
      const response = await api.post(`/api/v1/albums/${albumId}/reorder`, {
        image_ids: imageIds
      })
      
      return response.data
    } catch (err) {
      throw new Error(err.response?.data?.message || 'Failed to reorder album images')
    }
  }

  // Collection management
  const fetchCollections = async (params = {}) => {
    try {
      const response = await api.get('/api/v1/collections', { params })
      collections.value = response.data.data || response.data
      return response.data
    } catch (err) {
      throw new Error(err.response?.data?.message || 'Failed to fetch collections')
    }
  }

  const fetchCollection = async (id) => {
    try {
      const response = await api.get(`/api/v1/collections/${id}`)
      currentCollection.value = response.data
      return response.data
    } catch (err) {
      throw new Error(err.response?.data?.message || 'Failed to fetch collection')
    }
  }

  // Selection management
  const selectImage = (imageId) => {
    selectedImages.value.add(imageId)
  }

  const deselectImage = (imageId) => {
    selectedImages.value.delete(imageId)
  }

  const toggleImageSelection = (imageId) => {
    if (selectedImages.value.has(imageId)) {
      selectedImages.value.delete(imageId)
    } else {
      selectedImages.value.add(imageId)
    }
  }

  const selectAll = () => {
    images.value.forEach(image => {
      selectedImages.value.add(image.id)
    })
  }

  const deselectAll = () => {
    selectedImages.value.clear()
  }

  const getSelectedImageIds = () => {
    return Array.from(selectedImages.value)
  }

  // Bulk operations
  const bulkDelete = async (imageIds = null) => {
    const ids = imageIds || getSelectedImageIds()
    if (ids.length === 0) return

    try {
      await api.post('/api/v1/images/bulk', {
        action: 'delete',
        image_ids: ids
      })

      // Remove from local state
      images.value = images.value.filter(img => !ids.includes(img.id))
      ids.forEach(id => selectedImages.value.delete(id))
      
      return true
    } catch (err) {
      throw new Error(err.response?.data?.message || 'Failed to delete images')
    }
  }

  const bulkUpdatePrivacy = async (privacy, imageIds = null) => {
    const ids = imageIds || getSelectedImageIds()
    if (ids.length === 0) return

    try {
      const response = await api.post('/api/v1/images/bulk', {
        action: 'update',
        image_ids: ids,
        data: { privacy }
      })

      // Update local state
      images.value.forEach(img => {
        if (ids.includes(img.id)) {
          img.privacy = privacy
        }
      })
      
      return response.data
    } catch (err) {
      throw new Error(err.response?.data?.message || 'Failed to update privacy')
    }
  }

  const bulkAddTags = async (tags, imageIds = null) => {
    const ids = imageIds || getSelectedImageIds()
    if (ids.length === 0) return

    try {
      const response = await api.post('/api/v1/images/bulk', {
        action: 'add-tags',
        image_ids: ids,
        data: { tags }
      })

      return response.data
    } catch (err) {
      throw new Error(err.response?.data?.message || 'Failed to add tags')
    }
  }

  // Lightbox management
  const openLightbox = (imageIndex = 0, imageList = null) => {
    lightboxImages.value = imageList || images.value
    lightboxIndex.value = imageIndex
    lightboxOpen.value = true
  }

  const closeLightbox = () => {
    lightboxOpen.value = false
    lightboxIndex.value = 0
    lightboxImages.value = []
  }

  const nextImage = () => {
    if (lightboxIndex.value < lightboxImages.value.length - 1) {
      lightboxIndex.value++
    }
  }

  const previousImage = () => {
    if (lightboxIndex.value > 0) {
      lightboxIndex.value--
    }
  }

  // Filter and sorting
  const updateFilters = (newFilters) => {
    Object.assign(filters.value, newFilters)
    pagination.value.current_page = 1
  }

  const clearFilters = () => {
    filters.value = {
      privacy: null,
      tags: [],
      album_id: null,
      date_from: null,
      date_to: null,
      camera: null,
      orientation: null
    }
    pagination.value.current_page = 1
  }

  const updateSort = (field, order = null) => {
    if (sortBy.value === field && !order) {
      sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc'
    } else {
      sortBy.value = field
      sortOrder.value = order || 'desc'
    }
    pagination.value.current_page = 1
  }

  const setViewMode = (mode) => {
    viewMode.value = mode
    localStorage.setItem('gallery-view-mode', mode)
  }

  // Pagination
  const nextPage = () => {
    if (pagination.value.current_page < pagination.value.last_page) {
      pagination.value.current_page++
    }
  }

  const previousPage = () => {
    if (pagination.value.current_page > 1) {
      pagination.value.current_page--
    }
  }

  const goToPage = (page) => {
    if (page >= 1 && page <= pagination.value.last_page) {
      pagination.value.current_page = page
    }
  }

  // Reset store
  const resetStore = () => {
    images.value = []
    albums.value = []
    collections.value = []
    selectedImages.value.clear()
    currentAlbum.value = null
    currentCollection.value = null
    loading.value = false
    error.value = null
    pagination.value = {
      current_page: 1,
      last_page: 1,
      per_page: 20,
      total: 0
    }
    closeLightbox()
  }

  return {
    // State
    images,
    albums,
    collections,
    selectedImages,
    currentAlbum,
    currentCollection,
    loading,
    error,
    pagination,
    viewMode,
    sortBy,
    sortOrder,
    filters,
    lightboxOpen,
    lightboxIndex,
    lightboxImages,

    // Computed
    selectedCount,
    hasSelection,
    isAllSelected,
    activeFilters,

    // Image methods
    fetchImages,
    fetchMyImages,
    fetchImage,
    updateImage,
    deleteImage,
    likeImage,

    // Album methods
    fetchAlbums,
    fetchMyAlbums,
    fetchAlbum,
    createAlbum,
    updateAlbum,
    deleteAlbum,
    addImagesToAlbum,
    removeImagesFromAlbum,
    reorderAlbumImages,

    // Collection methods
    fetchCollections,
    fetchCollection,

    // Selection methods
    selectImage,
    deselectImage,
    toggleImageSelection,
    selectAll,
    deselectAll,
    getSelectedImageIds,

    // Bulk operations
    bulkDelete,
    bulkUpdatePrivacy,
    bulkAddTags,

    // Lightbox methods
    openLightbox,
    closeLightbox,
    nextImage,
    previousImage,

    // Filter and sorting
    updateFilters,
    clearFilters,
    updateSort,
    setViewMode,

    // Pagination
    nextPage,
    previousPage,
    goToPage,

    // Utility
    resetStore
  }
})