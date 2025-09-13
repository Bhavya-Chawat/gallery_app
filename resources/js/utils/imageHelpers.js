// Universal image URL generator
window.getImageUrl = function(imagePath, variant = 'medium') {
    if (!imagePath) return '/images/placeholder.jpg';
    
    // Check if we're in local development
    const isLocal = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
    
    if (isLocal) {
        // Local development - use MinIO
        return `http://localhost:9000/gallery-images/${imagePath}`;
    } else {
        // Production - use public storage
        return `${window.location.origin}/storage/${imagePath}`;
    }
};

// Avatar helper
window.getAvatarUrl = function(user) {
    if (!user || !user.avatar_path) return null;
    return window.getImageUrl(user.avatar_path);
};

// Export for ES modules too
export const getImageUrl = window.getImageUrl;
export const getAvatarUrl = window.getAvatarUrl;
