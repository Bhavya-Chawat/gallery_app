function getImageUrl(imagePath) {
  if (!imagePath) {
    return '/images/placeholder.jpg'
  }

  const baseUrl = window.location.origin
  const isLocal = baseUrl.includes('localhost') || baseUrl.includes('127.0.0.1')
  
  if (isLocal) {
    return `http://localhost:9000/gallery-images/${imagePath}`
  } else {
    return `${baseUrl}/storage/${imagePath}`
  }
}

function getAvatarUrl(user) {
  if (!user?.avatar_path) {
    return null
  }
  return getImageUrl(user.avatar_path)
}

function handleImageError(event, fallbackUrl = '/images/placeholder.jpg') {
  const img = event.target
  const currentSrc = img.src
  
  if (currentSrc.includes('localhost:9000')) {
    const imagePath = currentSrc.split('/').pop()
    img.src = `${window.location.origin}/storage/${imagePath}`
  } else {
    img.src = fallbackUrl
  }
}

// Export for ES modules
export { getImageUrl, getAvatarUrl, handleImageError }

// Also support CommonJS for Node.js compatibility
if (typeof module !== 'undefined' && module.exports) {
  module.exports = { getImageUrl, getAvatarUrl, handleImageError }
}
