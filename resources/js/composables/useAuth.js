// resources/js/composables/useAuth.js
import { ref, computed } from 'vue'
import axios from 'axios'

const user = ref(null)
const loading = ref(false)
const error = ref(null)

export function useAuth() {
  const isAuthenticated = computed(() => !!user.value)
  const isAdmin = computed(() => user.value?.roles?.some(role => role.name === 'admin') ?? false)
  const isModerator = computed(() => user.value?.roles?.some(role => ['admin', 'moderator'].includes(role.name)) ?? false)

  // Fetch current user info
  const fetchUser = async () => {
    if (loading.value) return user.value

    try {
      loading.value = true
      error.value = null
      const response = await axios.get('/api/v1/user')
      user.value = response.data.data
      return user.value
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch user'
      user.value = null
      throw err
    } finally {
      loading.value = false
    }
  }

  // Check if user has specific permission
  const can = (permission, resource = null) => {
    if (!user.value) return false
    
    // Admin can do everything
    if (isAdmin.value) return true
    
    // Check user permissions
    const userPermissions = user.value.permissions || []
    const rolePermissions = user.value.roles?.flatMap(role => role.permissions || []) || []
    const allPermissions = [...userPermissions, ...rolePermissions]
    
    return allPermissions.some(perm => {
      if (typeof perm === 'string') {
        return perm === permission
      }
      return perm.name === permission
    })
  }

  // Logout user
  const logout = async () => {
    try {
      await axios.post('/logout')
      user.value = null
    } catch (err) {
      console.error('Logout error:', err)
    }
  }

  // Initialize auth state (call this in app.js)
  const init = async () => {
    try {
      await fetchUser()
    } catch (err) {
      // User not authenticated, that's fine
      user.value = null
    }
  }

  return {
    user: computed(() => user.value),
    loading: computed(() => loading.value),
    error: computed(() => error.value),
    isAuthenticated,
    isAdmin,
    isModerator,
    fetchUser,
    can,
    logout,
    init
  }
}