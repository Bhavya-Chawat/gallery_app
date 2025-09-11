// /Users/kruthinipun/image_gallery/resources/js/Utils/api.js
import axios from 'axios'
import { router } from '@inertiajs/vue3'

// Configure axios defaults
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
axios.defaults.headers.common['Accept'] = 'application/json'
axios.defaults.headers.common['Content-Type'] = 'application/json'

// Add CSRF token
const token = document.head.querySelector('meta[name="csrf-token"]')
if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content
}

// Request interceptor
axios.interceptors.request.use(
    config => {
        // Add auth token if available
        const token = localStorage.getItem('auth_token')
        if (token) {
            config.headers.Authorization = `Bearer ${token}`
        }
        return config
    },
    error => Promise.reject(error)
)

// Response interceptor
axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response?.status === 401) {
            // Handle unauthorized - redirect to login
            localStorage.removeItem('auth_token')
            router.visit('/login')
        } else if (error.response?.status === 419) {
            // CSRF token mismatch - reload page
            window.location.reload()
        }
        return Promise.reject(error)
    }
)

// API endpoints based on mapping table
export const api = {
    // Public/Landing APIs
    images: {
        list: (params = {}) => axios.get('/api/v1/images', { params }),
        show: (id) => axios.get(`/api/v1/images/${id}`),
        like: (id) => axios.post(`/api/v1/images/${id}/like`),
        unlike: (id) => axios.delete(`/api/v1/images/${id}/like`),
        download: (id) => axios.get(`/api/v1/images/${id}/signed-url`),
        comments: (id, params = {}) => axios.get(`/api/v1/images/${id}/comments`, { params }),
        addComment: (id, data) => axios.post(`/api/v1/images/${id}/comments`, data),
        update: (id, data) => axios.put(`/api/v1/images/${id}`, data),
        delete: (id) => axios.delete(`/api/v1/images/${id}`),
        bulk: (data) => axios.post('/api/v1/images/bulk', data)
    },

    albums: {
        list: (params = {}) => axios.get('/api/v1/albums', { params }),
        show: (id) => axios.get(`/api/v1/albums/${id}`),
        create: (data) => axios.post('/api/v1/albums', data),
        update: (id, data) => axios.put(`/api/v1/albums/${id}`, data),
        delete: (id) => axios.delete(`/api/v1/albums/${id}`),
        reorder: (id, data) => axios.post(`/api/v1/albums/${id}/reorder`, data),
        addImages: (id, data) => axios.post(`/api/v1/albums/${id}/add-images`, data),
        removeImages: (id, data) => axios.delete(`/api/v1/albums/${id}/remove-images`, { data })
    },

    collections: {
        list: (params = {}) => axios.get('/api/v1/collections', { params }),
        show: (id) => axios.get(`/api/v1/collections/${id}`),
        create: (data) => axios.post('/api/v1/collections', data),
        update: (id, data) => axios.put(`/api/v1/collections/${id}`, data)
    },

    tags: {
        list: (params = {}) => axios.get('/api/v1/tags', { params }),
        show: (slug) => axios.get(`/api/v1/tags/${slug}`)
    },

    search: (params = {}) => axios.get('/api/v1/search', { params }),
    stats: () => axios.get('/api/v1/stats'),

    // Authenticated User APIs
    user: {
        profile: () => axios.get('/api/v1/user'),
        update: (data) => axios.put('/api/v1/user', data),
        delete: () => axios.delete('/api/v1/user')
    },

    my: {
        images: (params = {}) => axios.get('/api/v1/my/images', { params }),
        albums: (params = {}) => axios.get('/api/v1/my/albums', { params }),
        collections: (params = {}) => axios.get('/api/v1/my/collections', { params }),
        likes: (params = {}) => axios.get('/api/v1/my/likes', { params }),
        analytics: () => axios.get('/api/v1/my/analytics')
    },

    // Upload APIs
    upload: {
        presign: (data) => axios.post('/api/v1/upload/presign', data),
        complete: (data) => axios.post('/api/v1/upload/complete', data),
        status: (session) => axios.get(`/api/v1/upload/status/${session}`),
        direct: (data) => axios.post('/api/v1/upload/direct', data)
    },

    // Comments APIs
    comments: {
        update: (id, data) => axios.patch(`/api/v1/comments/${id}`, data),
        delete: (id) => axios.delete(`/api/v1/comments/${id}`),
        like: (id) => axios.post(`/api/v1/comments/${id}/like`),
        unlike: (id) => axios.delete(`/api/v1/comments/${id}/like`)
    },

    // Admin APIs
    admin: {
        stats: () => axios.get('/api/v1/admin/stats'),
        users: {
            list: (params = {}) => axios.get('/api/v1/admin/users', { params }),
            create: (data) => axios.post('/api/v1/admin/users', data),
            update: (id, data) => axios.put(`/api/v1/admin/users/${id}`, data),
            delete: (id) => axios.delete(`/api/v1/admin/users/${id}`)
        },
        moderation: {
            comments: (params = {}) => axios.get('/api/v1/admin/moderation/comments', { params }),
            approve: (type, id) => axios.post(`/api/v1/admin/moderation/${type}/${id}/approve`),
            reject: (type, id) => axios.post(`/api/v1/admin/moderation/${type}/${id}/reject`)
        },
        auditLogs: (params = {}) => axios.get('/api/v1/admin/audit-logs', { params })
    },

    // Health & System
    health: () => axios.get('/api/health')
}

// Helper functions for common patterns
export const apiHelpers = {
    // Handle paginated responses
    handlePaginated: (response) => ({
        data: response.data.data || [],
        meta: response.data.meta || {},
        links: response.data.links || {}
    }),

    // Handle API errors
    handleError: (error) => {
        if (error.response?.data?.message) {
            return error.response.data.message
        }
        return error.message || 'An unexpected error occurred'
    },

    // Build query string from params
    buildQuery: (params) => {
        const filtered = Object.entries(params)
            .filter(([_, value]) => value !== null && value !== undefined && value !== '')
            .reduce((obj, [key, value]) => ({ ...obj, [key]: value }), {})
        return new URLSearchParams(filtered).toString()
    }
}

export default axios