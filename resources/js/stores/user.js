import { defineStore } from 'pinia'
import { usePage } from '@inertiajs/vue3'
import axios from 'axios'

export const useUserStore = defineStore('user', {
  state: () => ({
    user: null,
    loading: false,
    error: null,
    preferences: {
      theme: 'system', // 'light', 'dark', 'system'
      defaultPrivacy: 'public',
      emailNotifications: true,
      browserNotifications: false,
      autoSave: true,
      compressUploads: true,
      preserveExif: false
    },
    quota: {
      used: 0,
      total: 0,
      remaining: 0
    },
    analytics: {
      totalImages: 0,
      totalViews: 0,
      totalLikes: 0,
      monthlyUploads: [],
      topTags: [],
      recentActivity: []
    }
  }),

  getters: {
    isAuthenticated: (state) => !!state.user,
    
    isAdmin: (state) => {
      return state.user?.roles?.some(role => role.name === 'admin') || false
    },
    
    isModerator: (state) => {
      return state.user?.roles?.some(role => ['admin', 'moderator'].includes(role.name)) || false
    },
    
    canUpload: (state) => {
      if (!state.user) return false
      return state.quota.remaining > 0 || state.quota.total === 0 // 0 means unlimited
    },
    
    storagePercentage: (state) => {
      if (!state.quota.total) return 0
      return Math.min((state.quota.used / state.quota.total) * 100, 100)
    },
    
    storageStatus: (state) => {
      const percentage = state.quota.total ? (state.quota.used / state.quota.total) * 100 : 0
      if (percentage > 95) return 'critical'
      if (percentage > 80) return 'warning'
      return 'normal'
    },
    
    fullName: (state) => state.user?.name || '',
    
    initials: (state) => {
      if (!state.user?.name) return ''
      return state.user.name
        .split(' ')
        .map(word => word.charAt(0))
        .join('')
        .toUpperCase()
        .slice(0, 2)
    },
    
    avatarUrl: (state) => state.user?.avatar_url || null,
    
    hasPermission: (state) => (permission, resource = null) => {
      if (!state.user) return false
      
      // Super admin has all permissions
      if (state.user.roles?.some(role => role.name === 'super_admin')) {
        return true
      }
      
      // Check specific permissions
      const userPermissions = state.user.permissions || []
      
      if (resource) {
        return userPermissions.some(p => 
          p.name === permission && (!p.resource || p.resource === resource)
        )
      }
      
      return userPermissions.some(p => p.name === permission)
    },
    
    can: (state) => (action, resource = null) => {
      // Alias for hasPermission for Laravel-style permission checking
      return state.hasPermission(action, resource)
    }
  },

  actions: {
    // Initialize user from Inertia page props
    initializeFromPage() {
      const page = usePage()
      if (page.props.auth?.user) {
        this.setUser(page.props.auth.user)
      }
      if (page.props.auth?.quota) {
        this.setQuota(page.props.auth.quota)
      }
    },

    // Set user data
    setUser(userData) {
      this.user = userData
      this.error = null
      
      // Load user preferences if available
      if (userData.preferences) {
        this.preferences = { ...this.preferences, ...userData.preferences }
      }
    },

    // Set quota information
    setQuota(quotaData) {
      this.quota = {
        used: quotaData.used || 0,
        total: quotaData.total || 0,
        remaining: Math.max(0, (quotaData.total || 0) - (quotaData.used || 0))
      }
    },

    // Update user profile
    async updateProfile(profileData) {
      this.loading = true
      try {
        const response = await axios.put('/api/v1/user/profile', profileData)
        this.setUser(response.data.data)
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to update profile'
        throw error
      } finally {
        this.loading = false
      }
    },

    // Update user preferences
    async updatePreferences(newPreferences) {
      this.loading = true
      try {
        const response = await axios.put('/api/v1/user/preferences', newPreferences)
        this.preferences = { ...this.preferences, ...newPreferences }
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to update preferences'
        throw error
      } finally {
        this.loading = false
      }
    },

    // Load user analytics
    async loadAnalytics() {
      this.loading = true
      try {
        const response = await axios.get('/api/v1/my/analytics')
        this.analytics = response.data.data
        return this.analytics
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to load analytics'
        throw error
      } finally {
        this.loading = false
      }
    },

    // Refresh user data
    async refresh() {
      this.loading = true
      try {
        const response = await axios.get('/api/v1/user')
        this.setUser(response.data.data)
        
        // Also refresh quota if available
        if (response.data.quota) {
          this.setQuota(response.data.quota)
        }
        
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to refresh user data'
        throw error
      } finally {
        this.loading = false
      }
    },

    // Upload avatar
    async uploadAvatar(file) {
      this.loading = true
      try {
        const formData = new FormData()
        formData.append('avatar', file)
        
        const response = await axios.post('/api/v1/user/avatar', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        })
        
        this.user.avatar_url = response.data.data.avatar_url
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to upload avatar'
        throw error
      } finally {
        this.loading = false
      }
    },

    // Delete avatar
    async deleteAvatar() {
      this.loading = true
      try {
        await axios.delete('/api/v1/user/avatar')
        this.user.avatar_url = null
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to delete avatar'
        throw error
      } finally {
        this.loading = false
      }
    },

    // Change password
    async changePassword(passwordData) {
      this.loading = true
      try {
        const response = await axios.put('/api/v1/user/password', passwordData)
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to change password'
        throw error
      } finally {
        this.loading = false
      }
    },

    // Generate API key
    async generateApiKey(name) {
      this.loading = true
      try {
        const response = await axios.post('/api/v1/user/api-keys', { name })
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to generate API key'
        throw error
      } finally {
        this.loading = false
      }
    },

    // Revoke API key
    async revokeApiKey(keyId) {
      this.loading = true
      try {
        await axios.delete(`/api/v1/user/api-keys/${keyId}`)
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to revoke API key'
        throw error
      } finally {
        this.loading = false
      }
    },

    // Update quota usage (called after upload/delete operations)
    updateQuotaUsage(bytesChanged) {
      this.quota.used = Math.max(0, this.quota.used + bytesChanged)
      this.quota.remaining = Math.max(0, this.quota.total - this.quota.used)
    },

    // Clear user data (logout)
    clear() {
      this.user = null
      this.error = null
      this.analytics = {
        totalImages: 0,
        totalViews: 0,
        totalLikes: 0,
        monthlyUploads: [],
        topTags: [],
        recentActivity: []
      }
      this.quota = {
        used: 0,
        total: 0,
        remaining: 0
      }
    },

    // Set loading state
    setLoading(loading) {
      this.loading = loading
    },

    // Set error
    setError(error) {
      this.error = error
    },

    // Clear error
    clearError() {
      this.error = null
    }
  }
})