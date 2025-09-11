// /Users/kruthinipun/image_gallery/resources/js/Utils/format.js

/**
 * Date formatting utilities
 */
export const dateFormatter = {
    // Format date for display (e.g., "Jan 15, 2024")
    short: (date) => {
        if (!date) return ''
        return new Intl.DateTimeFormat('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric'
        }).format(new Date(date))
    },

    // Format date with time (e.g., "Jan 15, 2024 at 3:45 PM")
    full: (date) => {
        if (!date) return ''
        return new Intl.DateTimeFormat('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric',
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        }).format(new Date(date))
    },

    // Relative time (e.g., "2 hours ago", "3 days ago")
    relative: (date) => {
        if (!date) return ''
        
        const now = new Date()
        const target = new Date(date)
        const diffInSeconds = Math.floor((now - target) / 1000)

        if (diffInSeconds < 60) return 'Just now'
        if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)} minutes ago`
        if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)} hours ago`
        if (diffInSeconds < 2592000) return `${Math.floor(diffInSeconds / 86400)} days ago`
        if (diffInSeconds < 31536000) return `${Math.floor(diffInSeconds / 2592000)} months ago`
        return `${Math.floor(diffInSeconds / 31536000)} years ago`
    },

    // ISO format for API calls
    iso: (date) => {
        if (!date) return ''
        return new Date(date).toISOString()
    },

    // Format for input[type="date"]
    inputDate: (date) => {
        if (!date) return ''
        return new Date(date).toISOString().split('T')[0]
    },

    // Format for input[type="datetime-local"]
    inputDateTime: (date) => {
        if (!date) return ''
        const d = new Date(date)
        const pad = (num) => num.toString().padStart(2, '0')
        return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`
    }
}

/**
 * File size formatting
 */
export const formatFileSize = (bytes, decimals = 2) => {
    if (!bytes || bytes === 0) return '0 B'

    const k = 1024
    const dm = decimals < 0 ? 0 : decimals
    const sizes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB']

    const i = Math.floor(Math.log(bytes) / Math.log(k))

    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i]
}

/**
 * Number formatting utilities
 */
export const numberFormatter = {
    // Compact numbers (1.2K, 3.4M, etc.)
    compact: (num) => {
        if (!num) return '0'
        
        const formatter = new Intl.NumberFormat('en-US', {
            notation: 'compact',
            maximumFractionDigits: 1
        })
        return formatter.format(num)
    },

    // With thousands separators (1,234,567)
    withSeparators: (num) => {
        if (!num) return '0'
        return new Intl.NumberFormat('en-US').format(num)
    },

    // Percentage (0.75 → 75%)
    percentage: (num, decimals = 0) => {
        if (num === null || num === undefined) return '0%'
        return `${(num * 100).toFixed(decimals)}%`
    },

    // Currency formatting
    currency: (amount, currency = 'USD') => {
        if (!amount) return '$0.00'
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency
        }).format(amount)
    }
}

/**
 * Text formatting utilities
 */
export const textFormatter = {
    // Truncate text with ellipsis
    truncate: (text, length = 100, suffix = '...') => {
        if (!text) return ''
        if (text.length <= length) return text
        return text.substring(0, length).trim() + suffix
    },

    // Capitalize first letter
    capitalize: (text) => {
        if (!text) return ''
        return text.charAt(0).toUpperCase() + text.slice(1).toLowerCase()
    },

    // Title case
    titleCase: (text) => {
        if (!text) return ''
        return text.split(' ')
            .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
            .join(' ')
    },

    // Slug from text
    slug: (text) => {
        if (!text) return ''
        return text
            .toLowerCase()
            .trim()
            .replace(/[^\w\s-]/g, '')
            .replace(/[\s_-]+/g, '-')
            .replace(/^-+|-+$/g, '')
    },

    // Extract initials (John Doe → JD)
    initials: (name) => {
        if (!name) return ''
        return name
            .split(' ')
            .map(word => word.charAt(0).toUpperCase())
            .join('')
            .substring(0, 2)
    },

    // Strip HTML tags
    stripHtml: (html) => {
        if (!html) return ''
        const div = document.createElement('div')
        div.innerHTML = html
        return div.textContent || div.innerText || ''
    }
}

/**
 * URL utilities
 */
export const urlFormatter = {
    // Build URL with query parameters
    buildUrl: (base, params = {}) => {
        const url = new URL(base, window.location.origin)
        Object.entries(params).forEach(([key, value]) => {
            if (value !== null && value !== undefined && value !== '') {
                url.searchParams.set(key, value)
            }
        })
        return url.toString()
    },

    // Extract filename from URL
    getFilename: (url) => {
        if (!url) return ''
        return url.split('/').pop().split('?')[0]
    },

    // Get file extension
    getExtension: (filename) => {
        if (!filename) return ''
        return filename.split('.').pop().toLowerCase()
    }
}

/**
 * Image utilities
 */
export const imageFormatter = {
    // Generate responsive image srcset
    generateSrcSet: (baseUrl, sizes = [400, 800, 1200, 1600]) => {
        return sizes
            .map(size => `${baseUrl}?w=${size} ${size}w`)
            .join(', ')
    },

    // Get image dimensions display text
    dimensionsText: (width, height) => {
        if (!width || !height) return ''
        return `${width} × ${height}`
    },

    // Calculate aspect ratio
    aspectRatio: (width, height) => {
        if (!width || !height) return 1
        return width / height
    },

    // Get orientation
    orientation: (width, height) => {
        if (!width || !height) return 'square'
        if (width > height) return 'landscape'
        if (height > width) return 'portrait'
        return 'square'
    }
}

/**
 * Color utilities for theming
 */
export const colorFormatter = {
    // Convert hex to RGB
    hexToRgb: (hex) => {
        const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex)
        return result ? {
            r: parseInt(result[1], 16),
            g: parseInt(result[2], 16),
            b: parseInt(result[3], 16)
        } : null
    },

    // Generate random color
    randomHex: () => {
        return '#' + Math.floor(Math.random()*16777215).toString(16)
    },

    // Get contrast color (black or white)
    getContrastColor: (hexcolor) => {
        const rgb = colorFormatter.hexToRgb(hexcolor)
        if (!rgb) return '#000000'
        
        const brightness = (rgb.r * 299 + rgb.g * 587 + rgb.b * 114) / 1000
        return brightness > 128 ? '#000000' : '#ffffff'
    }
}

/**
 * Validation utilities
 */
export const validators = {
    email: (email) => {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
        return re.test(email)
    },

    url: (url) => {
        try {
            new URL(url)
            return true
        } catch {
            return false
        }
    },

    // Image file types
    isImageFile: (filename) => {
        const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'tiff']
        const ext = urlFormatter.getExtension(filename)
        return imageExtensions.includes(ext)
    },

    // File size validation
    isValidFileSize: (file, maxSizeMB = 10) => {
        if (!file) return false
        return file.size <= maxSizeMB * 1024 * 1024
    }
}

/**
 * Animation and transition utilities
 */
export const animationHelpers = {
    // Easing functions
    easing: {
        easeInOut: 'cubic-bezier(0.4, 0, 0.2, 1)',
        easeOut: 'cubic-bezier(0, 0, 0.2, 1)',
        easeIn: 'cubic-bezier(0.4, 0, 1, 1)'
    },

    // Common durations
    duration: {
        fast: '150ms',
        normal: '300ms',
        slow: '500ms'
    }
}

/**
 * Export default object with all utilities
 */
export default {
    date: dateFormatter,
    fileSize: formatFileSize,
    number: numberFormatter,
    text: textFormatter,
    url: urlFormatter,
    image: imageFormatter,
    color: colorFormatter,
    validators,
    animation: animationHelpers
}