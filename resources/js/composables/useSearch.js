import { ref, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { debounce } from 'lodash'
import api from '@/Utils/api'

export function useSearch(options = {}) {
  const {
    endpoint = '/api/v1/search',
    debounceTime = 300,
    autoSearch = true,
    initialQuery = '',
    filters = {}
  } = options

  // Reactive state
  const query = ref(initialQuery)
  const results = ref([])
  const suggestions = ref([])
  const loading = ref(false)
  const error = ref(null)
  const hasSearched = ref(false)
  const activeFilters = ref({ ...filters })

  // Computed properties
  const hasResults = computed(() => results.value.length > 0)
  const isEmpty = computed(() => hasSearched.value && results.value.length === 0)
  const canSearch = computed(() => query.value.trim().length > 0)

  // Search function
  const performSearch = async (searchQuery = query.value, searchFilters = activeFilters.value) => {
    if (!searchQuery.trim()) {
      results.value = []
      hasSearched.value = false
      return
    }

    loading.value = true
    error.value = null

    try {
      const params = {
        q: searchQuery.trim(),
        ...searchFilters
      }

      const response = await api.get(endpoint, { params })
      
      results.value = response.data.data || response.data
      hasSearched.value = true
      
      // Store recent searches
      storeRecentSearch(searchQuery)
      
    } catch (err) {
      error.value = err.response?.data?.message || 'Search failed'
      results.value = []
    } finally {
      loading.value = false
    }
  }

  // Debounced search
  const debouncedSearch = debounce(performSearch, debounceTime)

  // Search suggestions
  const getSuggestions = async (searchQuery = query.value) => {
    if (!searchQuery.trim() || searchQuery.length < 2) {
      suggestions.value = []
      return
    }

    try {
      const response = await api.get(`${endpoint}/suggestions`, {
        params: { q: searchQuery.trim() }
      })
      
      suggestions.value = response.data.data || response.data
    } catch (err) {
      console.warn('Failed to fetch suggestions:', err)
      suggestions.value = []
    }
  }

  // Debounced suggestions
  const debouncedSuggestions = debounce(getSuggestions, 200)

  // Clear search
  const clearSearch = () => {
    query.value = ''
    results.value = []
    suggestions.value = []
    hasSearched.value = false
    error.value = null
  }

  // Filter management
  const setFilter = (key, value) => {
    if (value === null || value === undefined || value === '') {
      delete activeFilters.value[key]
    } else {
      activeFilters.value[key] = value
    }
    
    if (autoSearch && hasSearched.value) {
      debouncedSearch()
    }
  }

  const removeFilter = (key) => {
    delete activeFilters.value[key]
    
    if (autoSearch && hasSearched.value) {
      debouncedSearch()
    }
  }

  const clearFilters = () => {
    activeFilters.value = {}
    
    if (autoSearch && hasSearched.value) {
      debouncedSearch()
    }
  }

  // Recent searches management
  const getRecentSearches = () => {
    try {
      return JSON.parse(localStorage.getItem('recentSearches') || '[]')
    } catch {
      return []
    }
  }

  const storeRecentSearch = (searchQuery) => {
    try {
      const recent = getRecentSearches()
      const filtered = recent.filter(item => item !== searchQuery)
      const updated = [searchQuery, ...filtered].slice(0, 10) // Keep last 10 searches
      
      localStorage.setItem('recentSearches', JSON.stringify(updated))
    } catch (err) {
      console.warn('Failed to store recent search:', err)
    }
  }

  const clearRecentSearches = () => {
    try {
      localStorage.removeItem('recentSearches')
    } catch (err) {
      console.warn('Failed to clear recent searches:', err)
    }
  }

  // Search with navigation (for page-level searches)
  const searchWithNavigation = (searchQuery = query.value, route = 'gallery.index') => {
    if (!searchQuery.trim()) return

    router.get(route(route), {
      search: searchQuery.trim(),
      ...activeFilters.value
    }, {
      preserveState: true,
      preserveScroll: true
    })
  }

  // Advanced search builders
  const buildImageSearch = (options = {}) => {
    const {
      tags = [],
      album = null,
      dateFrom = null,
      dateTo = null,
      camera = null,
      orientation = null,
      privacy = null
    } = options

    const filters = {}
    
    if (tags.length > 0) filters.tags = tags.join(',')
    if (album) filters.album_id = album
    if (dateFrom) filters.date_from = dateFrom
    if (dateTo) filters.date_to = dateTo
    if (camera) filters.camera = camera
    if (orientation) filters.orientation = orientation
    if (privacy) filters.privacy = privacy

    Object.assign(activeFilters.value, filters)
    
    if (autoSearch) {
      debouncedSearch()
    }
  }

  const buildUserSearch = (options = {}) => {
    const {
      role = null,
      verified = null,
      active = null
    } = options

    const filters = {}
    
    if (role) filters.role = role
    if (verified !== null) filters.verified = verified
    if (active !== null) filters.active = active

    Object.assign(activeFilters.value, filters)
    
    if (autoSearch) {
      debouncedSearch()
    }
  }

  // Watch for query changes
  if (autoSearch) {
    watch(query, (newQuery) => {
      if (newQuery.trim()) {
        debouncedSearch(newQuery)
        debouncedSuggestions(newQuery)
      } else {
        clearSearch()
      }
    })
  }

  // Watch for suggestions
  watch(query, (newQuery) => {
    if (newQuery.trim().length >= 2) {
      debouncedSuggestions(newQuery)
    } else {
      suggestions.value = []
    }
  })

  return {
    // State
    query,
    results,
    suggestions,
    loading,
    error,
    hasSearched,
    activeFilters,

    // Computed
    hasResults,
    isEmpty,
    canSearch,

    // Methods
    performSearch,
    getSuggestions,
    clearSearch,
    setFilter,
    removeFilter,
    clearFilters,
    searchWithNavigation,
    buildImageSearch,
    buildUserSearch,

    // Recent searches
    getRecentSearches,
    clearRecentSearches
  }
}