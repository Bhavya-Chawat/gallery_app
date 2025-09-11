// /Users/kruthinipun/image_gallery/resources/js/Utils/permissions.js

import { usePage } from '@inertiajs/vue3'

/**
 * Role definitions matching backend permissions
 */
export const ROLES = {
    SUPER_ADMIN: 'super_admin',
    ADMIN: 'admin',
    MODERATOR: 'moderator', 
    EDITOR: 'editor',
    USER: 'user',
    GUEST: 'guest'
}

/**
 * Permission definitions matching backend abilities
 */
export const PERMISSIONS = {
    // User management
    MANAGE_USERS: 'manage_users',
    VIEW_USERS: 'view_users',
    CREATE_USERS: 'create_users',
    EDIT_USERS: 'edit_users',
    DELETE_USERS: 'delete_users',
    
    // Image management
    MANAGE_IMAGES: 'manage_images',
    VIEW_IMAGES: 'view_images',
    UPLOAD_IMAGES: 'upload_images',
    EDIT_IMAGES: 'edit_images',
    DELETE_IMAGES: 'delete_images',
    MODERATE_IMAGES: 'moderate_images',
    
    // Album management
    MANAGE_ALBUMS: 'manage_albums',
    VIEW_ALBUMS: 'view_albums',
    CREATE_ALBUMS: 'create_albums',
    EDIT_ALBUMS: 'edit_albums',
    DELETE_ALBUMS: 'delete_albums',
    
    // Collection management
    MANAGE_COLLECTIONS: 'manage_collections',
    VIEW_COLLECTIONS: 'view_collections',
    CREATE_COLLECTIONS: 'create_collections',
    EDIT_COLLECTIONS: 'edit_collections',
    DELETE_COLLECTIONS: 'delete_collections',
    CURATE_COLLECTIONS: 'curate_collections',
    
    // Comment management
    MANAGE_COMMENTS: 'manage_comments',
    VIEW_COMMENTS: 'view_comments',
    CREATE_COMMENTS: 'create_comments',
    EDIT_COMMENTS: 'edit_comments',
    DELETE_COMMENTS: 'delete_comments',
    MODERATE_COMMENTS: 'moderate_comments',
    
    // System administration
    MANAGE_SYSTEM: 'manage_system',
    VIEW_ADMIN_PANEL: 'view_admin_panel',
    VIEW_ANALYTICS: 'view_analytics',
    MANAGE_SETTINGS: 'manage_settings',
    VIEW_AUDIT_LOGS: 'view_audit_logs',
    MANAGE_API_KEYS: 'manage_api_keys',
    
    // Content moderation
    MODERATE_CONTENT: 'moderate_content',
    APPROVE_CONTENT: 'approve_content',
    REJECT_CONTENT: 'reject_content',
    FLAG_CONTENT: 'flag_content',
    
    // Privacy and visibility
    VIEW_PRIVATE_CONTENT: 'view_private_content',
    MANAGE_PRIVACY: 'manage_privacy'
}

/**
 * Role hierarchy (higher roles inherit permissions from lower roles)
 */
export const ROLE_HIERARCHY = {
    [ROLES.SUPER_ADMIN]: 100,
    [ROLES.ADMIN]: 80,
    [ROLES.MODERATOR]: 60,
    [ROLES.EDITOR]: 40,
    [ROLES.USER]: 20,
    [ROLES.GUEST]: 0
}

/**
 * Default permissions for each role
 */
export const ROLE_PERMISSIONS = {
    [ROLES.SUPER_ADMIN]: [
        // All permissions - super admin can do everything
        ...Object.values(PERMISSIONS)
    ],
    
    [ROLES.ADMIN]: [
        PERMISSIONS.MANAGE_USERS,
        PERMISSIONS.VIEW_USERS,
        PERMISSIONS.CREATE_USERS,
        PERMISSIONS.EDIT_USERS,
        PERMISSIONS.DELETE_USERS,
        PERMISSIONS.MANAGE_IMAGES,
        PERMISSIONS.VIEW_IMAGES,
        PERMISSIONS.UPLOAD_IMAGES,
        PERMISSIONS.EDIT_IMAGES,
        PERMISSIONS.DELETE_IMAGES,
        PERMISSIONS.MODERATE_IMAGES,
        PERMISSIONS.MANAGE_ALBUMS,
        PERMISSIONS.VIEW_ALBUMS,
        PERMISSIONS.CREATE_ALBUMS,
        PERMISSIONS.EDIT_ALBUMS,
        PERMISSIONS.DELETE_ALBUMS,
        PERMISSIONS.MANAGE_COLLECTIONS,
        PERMISSIONS.VIEW_COLLECTIONS,
        PERMISSIONS.CREATE_COLLECTIONS,
        PERMISSIONS.EDIT_COLLECTIONS,
        PERMISSIONS.DELETE_COLLECTIONS,
        PERMISSIONS.CURATE_COLLECTIONS,
        PERMISSIONS.MANAGE_COMMENTS,
        PERMISSIONS.VIEW_COMMENTS,
        PERMISSIONS.CREATE_COMMENTS,
        PERMISSIONS.EDIT_COMMENTS,
        PERMISSIONS.DELETE_COMMENTS,
        PERMISSIONS.MODERATE_COMMENTS,
        PERMISSIONS.VIEW_ADMIN_PANEL,
        PERMISSIONS.VIEW_ANALYTICS,
        PERMISSIONS.MANAGE_SETTINGS,
        PERMISSIONS.VIEW_AUDIT_LOGS,
        PERMISSIONS.MANAGE_API_KEYS,
        PERMISSIONS.MODERATE_CONTENT,
        PERMISSIONS.APPROVE_CONTENT,
        PERMISSIONS.REJECT_CONTENT,
        PERMISSIONS.FLAG_CONTENT,
        PERMISSIONS.VIEW_PRIVATE_CONTENT,
        PERMISSIONS.MANAGE_PRIVACY
    ],
    
    [ROLES.MODERATOR]: [
        PERMISSIONS.VIEW_IMAGES,
        PERMISSIONS.UPLOAD_IMAGES,
        PERMISSIONS.EDIT_IMAGES,
        PERMISSIONS.MODERATE_IMAGES,
        PERMISSIONS.VIEW_ALBUMS,
        PERMISSIONS.CREATE_ALBUMS,
        PERMISSIONS.EDIT_ALBUMS,
        PERMISSIONS.VIEW_COLLECTIONS,
        PERMISSIONS.CREATE_COLLECTIONS,
        PERMISSIONS.EDIT_COLLECTIONS,
        PERMISSIONS.VIEW_COMMENTS,
        PERMISSIONS.CREATE_COMMENTS,
        PERMISSIONS.EDIT_COMMENTS,
        PERMISSIONS.MODERATE_COMMENTS,
        PERMISSIONS.MODERATE_CONTENT,
        PERMISSIONS.APPROVE_CONTENT,
        PERMISSIONS.REJECT_CONTENT,
        PERMISSIONS.FLAG_CONTENT
    ],
    
    [ROLES.EDITOR]: [
        PERMISSIONS.VIEW_IMAGES,
        PERMISSIONS.UPLOAD_IMAGES,
        PERMISSIONS.EDIT_IMAGES,
        PERMISSIONS.VIEW_ALBUMS,
        PERMISSIONS.CREATE_ALBUMS,
        PERMISSIONS.EDIT_ALBUMS,
        PERMISSIONS.VIEW_COLLECTIONS,
        PERMISSIONS.CREATE_COLLECTIONS,
        PERMISSIONS.EDIT_COLLECTIONS,
        PERMISSIONS.CURATE_COLLECTIONS,
        PERMISSIONS.VIEW_COMMENTS,
        PERMISSIONS.CREATE_COMMENTS,
        PERMISSIONS.EDIT_COMMENTS,
        PERMISSIONS.FLAG_CONTENT
    ],
    
    [ROLES.USER]: [
        PERMISSIONS.VIEW_IMAGES,
        PERMISSIONS.UPLOAD_IMAGES,
        PERMISSIONS.VIEW_ALBUMS,
        PERMISSIONS.CREATE_ALBUMS,
        PERMISSIONS.VIEW_COLLECTIONS,
        PERMISSIONS.VIEW_COMMENTS,
        PERMISSIONS.CREATE_COMMENTS,
        PERMISSIONS.FLAG_CONTENT
    ],
    
    [ROLES.GUEST]: [
        PERMISSIONS.VIEW_IMAGES,
        PERMISSIONS.VIEW_ALBUMS,
        PERMISSIONS.VIEW_COLLECTIONS,
        PERMISSIONS.VIEW_COMMENTS
    ]
}

/**
 * Permission checker class
 */
export class PermissionChecker {
    constructor(user = null) {
        this.user = user
    }
    
    /**
     * Update current user
     */
    setUser(user) {
        this.user = user
        return this
    }
    
    /**
     * Check if user has specific permission
     */
    can(permission, resource = null) {
        if (!this.user) {
            return ROLE_PERMISSIONS[ROLES.GUEST].includes(permission)
        }
        
        // Super admin can do everything
        if (this.user.role === ROLES.SUPER_ADMIN) {
            return true
        }
        
        // Check if user's role has the permission
        const userPermissions = ROLE_PERMISSIONS[this.user.role] || []
        const hasPermission = userPermissions.includes(permission)
        
        // If checking resource ownership
        if (resource && hasPermission) {
            return this.ownsResource(resource) || this.canManageAnyResource(permission)
        }
        
        return hasPermission
    }
    
    /**
     * Check if user cannot perform action
     */
    cannot(permission, resource = null) {
        return !this.can(permission, resource)
    }
    
    /**
     * Check if user has any of the given permissions
     */
    canAny(permissions, resource = null) {
        return permissions.some(permission => this.can(permission, resource))
    }
    
    /**
     * Check if user has all given permissions
     */
    canAll(permissions, resource = null) {
        return permissions.every(permission => this.can(permission, resource))
    }
    
    /**
     * Check if user has specific role
     */
    hasRole(role) {
        return this.user?.role === role
    }
    
    /**
     * Check if user has any of given roles
     */
    hasAnyRole(roles) {
        return roles.includes(this.user?.role)
    }
    
    /**
     * Check if user's role is at least the given role level
     */
    hasRoleLevel(role) {
        if (!this.user?.role) return false
        
        const userLevel = ROLE_HIERARCHY[this.user.role] || 0
        const requiredLevel = ROLE_HIERARCHY[role] || 0
        
        return userLevel >= requiredLevel
    }
    
    /**
     * Check if user owns the resource
     */
    ownsResource(resource) {
        if (!this.user || !resource) return false
        
        // Check various ownership fields
        return resource.user_id === this.user.id ||
               resource.owner_id === this.user.id ||
               resource.created_by === this.user.id ||
               resource.author_id === this.user.id
    }
    
    /**
     * Check if user can manage any resource of this type (admin/moderator level)
     */
    canManageAnyResource(permission) {
        if (!this.user) return false
        
        return this.hasRoleLevel(ROLES.MODERATOR) && 
               permission.startsWith('edit_') || 
               permission.startsWith('delete_') || 
               permission.startsWith('manage_')
    }
    
    /**
     * Check if user is authenticated
     */
    isAuthenticated() {
        return !!this.user
    }
    
    /**
     * Check if user is guest
     */
    isGuest() {
        return !this.user
    }
    
    /**
     * Check if user is admin level (admin or super admin)
     */
    isAdmin() {
        return this.hasAnyRole([ROLES.ADMIN, ROLES.SUPER_ADMIN])
    }
    
    /**
     * Check if user is moderator level or higher
     */
    isModerator() {
        return this.hasRoleLevel(ROLES.MODERATOR)
    }
    
    /**
     * Get user's permissions array
     */
    getUserPermissions() {
        if (!this.user?.role) {
            return ROLE_PERMISSIONS[ROLES.GUEST] || []
        }
        
        return ROLE_PERMISSIONS[this.user.role] || []
    }
    
    /**
     * Get user's role display name
     */
    getRoleDisplayName() {
        if (!this.user?.role) return 'Guest'
        
        const roleNames = {
            [ROLES.SUPER_ADMIN]: 'Super Administrator',
            [ROLES.ADMIN]: 'Administrator', 
            [ROLES.MODERATOR]: 'Moderator',
            [ROLES.EDITOR]: 'Editor',
            [ROLES.USER]: 'User',
            [ROLES.GUEST]: 'Guest'
        }
        
        return roleNames[this.user.role] || 'Unknown'
    }
}

/**
 * Global permission checker instance
 */
let globalPermissionChecker = new PermissionChecker()

/**
 * Core permission functions
 */
export const permissions = () => globalPermissionChecker

export const initPermissions = (user) => {
    globalPermissionChecker.setUser(user)
    return globalPermissionChecker
}

export const can = (permission, resource = null) => {
    return globalPermissionChecker.can(permission, resource)
}

export const cannot = (permission, resource = null) => {
    return globalPermissionChecker.cannot(permission, resource)
}

/**
 * Single implementation of hasRole
 */
export const hasRole = (roles) => {
    const { auth } = usePage().props
    
    // Admin has all roles
    if (auth.user?.is_admin) {
        return true
    }
    
    // Convert single role to array
    const rolesToCheck = Array.isArray(roles) ? roles : [roles]
    
    // Check user roles from auth
    return rolesToCheck.some(role => auth.user?.roles?.includes(role))
}

export const isAdmin = () => {
    return globalPermissionChecker.isAdmin()
}

export const isModerator = () => {
    return globalPermissionChecker.isModerator()
}

export const isAuthenticated = () => {
    return globalPermissionChecker.isAuthenticated()
}

/**
 * Vue.js directive for v-can
 * Usage: v-can:edit="image" or v-can="'manage_users'"
 */
export const vCanDirective = {
    mounted(el, binding) {
        const permission = binding.arg || binding.value
        const resource = binding.arg ? binding.value : null
        
        if (!can(permission, resource)) {
            el.style.display = 'none'
        }
    },
    
    updated(el, binding) {
        const permission = binding.arg || binding.value
        const resource = binding.arg ? binding.value : null
        
        if (!can(permission, resource)) {
            el.style.display = 'none'
        } else {
            el.style.display = ''
        }
    }
}

/**
 * Route middleware for permission checking
 */
export const requiresPermission = (permission, redirectTo = '/unauthorized') => {
    return (to, from, next) => {
        if (can(permission)) {
            next()
        } else {
            next(redirectTo)
        }
    }
}

export const requiresRole = (role, redirectTo = '/unauthorized') => {
    return (to, from, next) => {
        if (hasRole(role)) {
            next()
        } else {
            next(redirectTo)
        }
    }
}

export const requiresAuth = (redirectTo = '/login') => {
    return (to, from, next) => {
        if (isAuthenticated()) {
            next()
        } else {
            next(redirectTo)
        }
    }
}

/**
 * Content visibility helpers
 */
export const contentVisibility = {
    /**
     * Check if user can view content based on privacy settings
     */
    canView(content, user = null) {
        if (!content) return false
        
        const checker = user ? new PermissionChecker(user) : globalPermissionChecker
        
        // Public content is always visible
        if (content.privacy === 'public') return true
        
        // Private content only visible to owner and admins
        if (content.privacy === 'private') {
            return checker.ownsResource(content) || 
                   checker.can(PERMISSIONS.VIEW_PRIVATE_CONTENT)
        }
        
        // Unlisted content visible to authenticated users
        if (content.privacy === 'unlisted') {
            return checker.isAuthenticated()
        }
        
        // Draft content only visible to owner and editors
        if (content.status === 'draft') {
            return checker.ownsResource(content) || 
                   checker.can(PERMISSIONS.MODERATE_CONTENT)
        }
        
        // Pending content visible to moderators
        if (content.status === 'pending') {
            return checker.ownsResource(content) || 
                   checker.can(PERMISSIONS.MODERATE_CONTENT)
        }
        
        return true
    },
    
    /**
     * Filter content array based on visibility
     */
    filterVisible(contents, user = null) {
        return contents.filter(content => this.canView(content, user))
    },
    
    /**
     * Get privacy options for user
     */
    getPrivacyOptions(user = null) {
        const checker = user ? new PermissionChecker(user) : globalPermissionChecker
        
        const options = [
            { value: 'public', label: 'Public', description: 'Anyone can see this' }
        ]
        
        if (checker.isAuthenticated()) {
            options.push(
                { value: 'unlisted', label: 'Unlisted', description: 'Only people with the link can see this' },
                { value: 'private', label: 'Private', description: 'Only you can see this' }
            )
        }
        
        return options
    }
}

/**
 * Bulk action helpers
 */
export const bulkActions = {
    /**
     * Get available bulk actions for selected items
     */
    getAvailableActions(items, user = null) {
        const checker = user ? new PermissionChecker(user) : globalPermissionChecker
        const actions = []
        
        // Check if user can edit any of the selected items
        const canEditAny = items.some(item => 
            checker.can(PERMISSIONS.EDIT_IMAGES, item) ||
            checker.can(PERMISSIONS.EDIT_ALBUMS, item)
        )
        
        // Check if user can delete any of the selected items  
        const canDeleteAny = items.some(item =>
            checker.can(PERMISSIONS.DELETE_IMAGES, item) ||
            checker.can(PERMISSIONS.DELETE_ALBUMS, item)
        )
        
        if (canEditAny) {
            actions.push(
                { key: 'edit_metadata', label: 'Edit Metadata', icon: 'Edit' },
                { key: 'move_to_album', label: 'Move to Album', icon: 'FolderPlus' },
                { key: 'change_privacy', label: 'Change Privacy', icon: 'Lock' }
            )
        }
        
        if (checker.can(PERMISSIONS.MODERATE_CONTENT)) {
            actions.push(
                { key: 'approve', label: 'Approve', icon: 'Check' },
                { key: 'feature', label: 'Feature', icon: 'Star' }
            )
        }
        
        if (canDeleteAny) {
            actions.push(
                { key: 'delete', label: 'Delete', icon: 'Trash2', destructive: true }
            )
        }
        
        return actions
    }
}

/**
 * Export default permissions object
 */
export default {
    ROLES,
    PERMISSIONS,
    ROLE_HIERARCHY,
    ROLE_PERMISSIONS,
    PermissionChecker,
    initPermissions,
    permissions,
    can,
    cannot,
    hasRole,
    isAdmin,
    isModerator,
    isAuthenticated,
    vCanDirective,
    requiresPermission,
    requiresRole,
    requiresAuth,
    contentVisibility,
    bulkActions
}