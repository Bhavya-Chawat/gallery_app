<template>
  <svg
    :class="className"
    viewBox="0 0 24 24"
    fill="none"
    xmlns="http://www.w3.org/2000/svg"
  >
    <defs>
      <linearGradient id="logoGradient" x1="0%" y1="0%" x2="100%" y2="100%">
        <stop offset="0%" style="stop-color:#2563EB;stop-opacity:1" />
        <stop offset="50%" style="stop-color:#06B6D4;stop-opacity:1" />
        <stop offset="100%" style="stop-color:#60A5FA;stop-opacity:1" />
      </linearGradient>
    </defs>
    
    <!-- Camera Body -->
    <path
      d="M7 4V2C7 1.44772 7.44772 1 8 1H16C16.5523 1 17 1.44772 17 2V4H20C21.1046 4 22 4.89543 22 6V18C22 19.1046 21.1046 20 20 20H4C2.89543 20 2 19.1046 2 18V6C2 4.89543 2.89543 4 4 4H7Z"
      fill="url(#logoGradient)"
    />
    
    <!-- Lens -->
    <circle
      cx="12"
      cy="12"
      r="4"
      fill="none"
      stroke="white"
      stroke-width="2"
    />
    
    <!-- Inner Lens -->
    <circle
      cx="12"
      cy="12"
      r="2.5"
      fill="none"
      stroke="white"
      stroke-width="1.5"
      opacity="0.7"
    />
    
    <!-- Flash -->
    <rect
      x="16"
      y="6"
      width="2"
      height="1.5"
      rx="0.5"
      fill="white"
      opacity="0.9"
    />
    
    <!-- Viewfinder -->
    <rect
      x="5"
      y="6"
      width="1.5"
      height="1"
      rx="0.25"
      fill="white"
      opacity="0.9"
    />
  </svg>
</template>

<script setup>
defineProps({
  className: {
    type: String,
    default: 'h-8 w-8'
  }
})
</script>