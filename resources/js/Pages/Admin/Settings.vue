<template>
  <AdminLayout>
    <div class="min-h-screen bg-gray-50 dark:bg-slate-900">
      <!-- Header -->
      <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl border-b border-gray-200 dark:border-slate-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                Settings
              </h1>
              <p class="text-gray-500 dark:text-slate-400 mt-1">
                Configure system settings and preferences
              </p>
            </div>
            
            <button
              @click="saveSettings"
              :disabled="saving"
              class="bg-gradient-to-r from-blue-600 via-cyan-500 to-blue-400 text-white px-6 py-2 rounded-lg hover:shadow-lg transition-all duration-200 font-medium disabled:opacity-50"
            >
              <Save class="w-4 h-4 inline mr-2" />
              {{ saving ? 'Saving...' : 'Save Changes' }}
            </button>
          </div>
        </div>
      </div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col lg:flex-row gap-8">
          <!-- Navigation Sidebar -->
          <div class="lg:w-64 flex-shrink-0">
            <nav class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl rounded-xl border border-gray-200 dark:border-slate-700 p-4">
              <div class="space-y-2">
                <button
                  v-for="section in sections"
                  :key="section.key"
                  @click="activeSection = section.key"
                  :class="{
                    'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300': activeSection === section.key,
                    'text-gray-600 dark:text-slate-400 hover:text-gray-900 dark:hover:text-slate-200 hover:bg-gray-50 dark:hover:bg-slate-700/50': activeSection !== section.key
                  }"
                  class="w-full flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors"
                >
                  <component :is="section.icon" class="w-4 h-4 mr-3" />
                  {{ section.name }}
                </button>
              </div>
            </nav>
          </div>

          <!-- Settings Content -->
          <div class="flex-1">
            <!-- General Settings -->
            <div v-if="activeSection === 'general'" class="space-y-6">
              <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl rounded-xl border border-gray-200 dark:border-slate-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                  General Settings
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
                      Site Name
                    </label>
                    <input
                      v-model="settings.general.siteName"
                      type="text"
                      class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    />
                  </div>
                  
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
                      Site URL
                    </label>
                    <input
                      v-model="settings.general.siteUrl"
                      type="url"
                      class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    />
                  </div>
                  
                  <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
                      Site Description
                    </label>
                    <textarea
                      v-model="settings.general.siteDescription"
                      rows="3"
                      class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    ></textarea>
                  </div>
                  
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
                      Admin Email
                    </label>
                    <input
                      v-model="settings.general.adminEmail"
                      type="email"
                      class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    />
                  </div>
                  
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
                      Timezone
                    </label>
                    <select
                      v-model="settings.general.timezone"
                      class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                      <option value="UTC">UTC</option>
                      <option value="America/New_York">Eastern Time</option>
                      <option value="America/Chicago">Central Time</option>
                      <option value="America/Denver">Mountain Time</option>
                      <option value="America/Los_Angeles">Pacific Time</option>
                      <option value="Europe/London">London</option>
                      <option value="Europe/Paris">Paris</option>
                      <option value="Asia/Tokyo">Tokyo</option>
                    </select>
                  </div>
                </div>
                
                <div class="mt-6 space-y-4">
                  <div class="flex items-center">
                    <input
                      v-model="settings.general.registrationEnabled"
                      type="checkbox"
                      class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    />
                    <label class="ml-2 text-sm text-gray-700 dark:text-slate-300">
                      Allow user registration
                    </label>
                  </div>
                  
                  <div class="flex items-center">
                    <input
                      v-model="settings.general.maintenanceMode"
                      type="checkbox"
                      class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    />
                    <label class="ml-2 text-sm text-gray-700 dark:text-slate-300">
                      Enable maintenance mode
                    </label>
                  </div>
                </div>
              </div>
            </div>

            <!-- Email Settings -->
            <div v-if="activeSection === 'email'" class="space-y-6">
              <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl rounded-xl border border-gray-200 dark:border-slate-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                  Email Configuration
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
                      SMTP Host
                    </label>
                    <input
                      v-model="settings.email.smtpHost"
                      type="text"
                      class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    />
                  </div>
                  
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
                      SMTP Port
                    </label>
                    <input
                      v-model.number="settings.email.smtpPort"
                      type="number"
                      class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    />
                  </div>
                  
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
                      From Email
                    </label>
                    <input
                      v-model="settings.email.fromEmail"
                      type="email"
                      class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    />
                  </div>
                  
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
                      From Name
                    </label>
                    <input
                      v-model="settings.email.fromName"
                      type="text"
                      class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    />
                  </div>
                </div>
                
                <div class="mt-6">
                  <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-4">
                    Email Notifications
                  </h4>
                  
                  <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-slate-700/50 rounded-lg">
                      <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">New User Registration</p>
                        <p class="text-xs text-gray-500 dark:text-slate-400">Send email when new users register</p>
                      </div>
                      <input
                        v-model="settings.email.notifications.newUser"
                        type="checkbox"
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                      />
                    </div>
                    
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-slate-700/50 rounded-lg">
                      <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Image Upload</p>
                        <p class="text-xs text-gray-500 dark:text-slate-400">Send email when images are uploaded</p>
                      </div>
                      <input
                        v-model="settings.email.notifications.imageUpload"
                        type="checkbox"
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                      />
                    </div>
                    
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-slate-700/50 rounded-lg">
                      <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Content Reports</p>
                        <p class="text-xs text-gray-500 dark:text-slate-400">Send email when content is reported</p>
                      </div>
                      <input
                        v-model="settings.email.notifications.contentReport"
                        type="checkbox"
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                      />
                    </div>
                  </div>
                </div>
                
                <div class="mt-6">
                  <button
                    @click="testEmail"
                    :disabled="testingEmail"
                    class="bg-green-600 hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed text-white px-4 py-2 rounded-lg transition-colors"
                  >
                    <Mail class="w-4 h-4 inline mr-2" />
                    {{ testingEmail ? 'Sending Test...' : 'Send Test Email' }}
                  </button>
                </div>
              </div>
            </div>

            <!-- Security Settings -->
            <div v-if="activeSection === 'security'" class="space-y-6">
              <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl rounded-xl border border-gray-200 dark:border-slate-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                  Security & Privacy
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
                      Session Timeout (minutes)
                    </label>
                    <input
                      v-model.number="settings.security.sessionTimeout"
                      type="number"
                      min="15"
                      max="1440"
                      class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    />
                  </div>
                  
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
                      Max Login Attempts
                    </label>
                    <input
                      v-model.number="settings.security.maxLoginAttempts"
                      type="number"
                      min="3"
                      max="10"
                      class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    />
                  </div>
                  
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
                      Lockout Duration (minutes)
                    </label>
                    <input
                      v-model.number="settings.security.lockoutDuration"
                      type="number"
                      min="5"
                      max="60"
                      class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    />
                  </div>
                  
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
                      Password Minimum Length
                    </label>
                    <input
                      v-model.number="settings.security.passwordMinLength"
                      type="number"
                      min="6"
                      max="32"
                      class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    />
                  </div>
                </div>
                
                <div class="mt-6 space-y-4">
                  <div class="flex items-center">
                    <input
                      v-model="settings.security.requireEmailVerification"
                      type="checkbox"
                      class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    />
                    <label class="ml-2 text-sm text-gray-700 dark:text-slate-300">
                      Require email verification for new accounts
                    </label>
                  </div>
                  
                  <div class="flex items-center">
                    <input
                      v-model="settings.security.enable2FA"
                      type="checkbox"
                      class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    />
                    <label class="ml-2 text-sm text-gray-700 dark:text-slate-300">
                      Enable two-factor authentication
                    </label>
                  </div>
                  
                  <div class="flex items-center">
                    <input
                      v-model="settings.security.forceHttps"
                      type="checkbox"
                      class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    />
                    <label class="ml-2 text-sm text-gray-700 dark:text-slate-300">
                      Force HTTPS connections
                    </label>
                  </div>
                  
                  <div class="flex items-center">
                    <input
                      v-model="settings.security.enableCaptcha"
                      type="checkbox"
                      class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    />
                    <label class="ml-2 text-sm text-gray-700 dark:text-slate-300">
                      Enable CAPTCHA for forms
                    </label>
                  </div>
                </div>
              </div>
              
              <!-- API Keys Management -->
              <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl rounded-xl border border-gray-200 dark:border-slate-700 p-6">
                <div class="flex items-center justify-between mb-4">
                  <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    API Keys
                  </h3>
                  <button
                    @click="showCreateApiKey = true"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition-colors"
                  >
                    <Plus class="w-4 h-4 inline mr-2" />
                    Generate API Key
                  </button>
                </div>
                
                <div class="space-y-4">
                  <div 
                    v-for="apiKey in apiKeys" 
                    :key="apiKey.id"
                    class="flex items-center justify-between p-4 bg-gray-50 dark:bg-slate-700/50 rounded-lg"
                  >
                    <div>
                      <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ apiKey.name }}</p>
                      <p class="text-xs text-gray-500 dark:text-slate-400">
                        Created {{ formatDate(apiKey.created_at) }} • Last used {{ apiKey.last_used || 'Never' }}
                      </p>
                    </div>
                    <div class="flex items-center space-x-2">
                      <span 
                        :class="{
                          'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400': apiKey.status === 'active',
                          'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400': apiKey.status === 'revoked'
                        }"
                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                      >
                        {{ apiKey.status }}
                      </span>
                      <button
                        @click="revokeApiKey(apiKey.id)"
                        class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 p-1"
                      >
                        <Trash2 class="w-4 h-4" />
                      </button>
                    </div>
                  </div>
                  
                  <div v-if="apiKeys.length === 0" class="text-center py-8 text-gray-500 dark:text-slate-400">
                    No API keys generated yet
                  </div>
                </div>
              </div>
            </div>

            <!-- Upload Settings -->
            <div v-if="activeSection === 'uploads'" class="space-y-6">
              <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl rounded-xl border border-gray-200 dark:border-slate-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                  File Upload Settings
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
                      Max File Size (MB)
                    </label>
                    <input
                      v-model.number="settings.uploads.maxFileSize"
                      type="number"
                      min="1"
                      max="100"
                      class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    />
                  </div>
                  
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
                      Max Images Per User
                    </label>
                    <input
                      v-model.number="settings.uploads.maxImagesPerUser"
                      type="number"
                      min="10"
                      class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    />
                  </div>
                  
                  <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
                      Allowed File Types
                    </label>
                    <input
                      v-model="settings.uploads.allowedTypes"
                      type="text"
                      placeholder="jpg,jpeg,png,gif,webp"
                      class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    />
                  </div>
                </div>
                
                <div class="mt-6 space-y-4">
                  <div class="flex items-center">
                    <input
                      v-model="settings.uploads.autoOptimize"
                      type="checkbox"
                      class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    />
                    <label class="ml-2 text-sm text-gray-700 dark:text-slate-300">
                      Auto-optimize images on upload
                    </label>
                  </div>
                  
                  <div class="flex items-center">
                    <input
                      v-model="settings.uploads.generateThumbnails"
                      type="checkbox"
                      class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    />
                    <label class="ml-2 text-sm text-gray-700 dark:text-slate-300">
                      Generate thumbnails automatically
                    </label>
                  </div>
                  
                  <div class="flex items-center">
                    <input
                      v-model="settings.uploads.requireModeration"
                      type="checkbox"
                      class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    />
                    <label class="ml-2 text-sm text-gray-700 dark:text-slate-300">
                      Require moderation before publishing
                    </label>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Create API Key Modal -->
    <div 
      v-if="showCreateApiKey"
      class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50"
      @click.self="showCreateApiKey = false"
    >
      <div class="bg-white dark:bg-slate-800 rounded-xl p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
          Generate API Key
        </h3>
        
        <form @submit.prevent="createApiKey" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
              Key Name
            </label>
            <input
              v-model="newApiKey.name"
              type="text"
              required
              placeholder="e.g., Mobile App, Third Party Service"
              class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
              Description
            </label>
            <textarea
              v-model="newApiKey.description"
              rows="3"
              placeholder="What will this API key be used for?"
              class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            ></textarea>
          </div>

          <div class="flex justify-end space-x-3 pt-4">
            <button
              type="button"
              @click="showCreateApiKey = false"
              class="text-gray-600 dark:text-slate-400 hover:text-gray-800 dark:hover:text-slate-200"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="creatingApiKey"
              class="bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed text-white px-4 py-2 rounded-lg transition-colors"
            >
              {{ creatingApiKey ? 'Generating...' : 'Generate Key' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Generated API Key Modal -->
    <div 
      v-if="showGeneratedKey"
      class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50"
      @click.self="showGeneratedKey = false"
    >
      <div class="bg-white dark:bg-slate-800 rounded-xl p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
          API Key Generated
        </h3>
        
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-4">
          <p class="text-sm text-yellow-800 dark:text-yellow-200 mb-2">
            <strong>Important:</strong> Copy this API key now. You won't be able to see it again.
          </p>
        </div>
        
        <div class="bg-gray-50 dark:bg-slate-700 rounded-lg p-3 mb-4">
          <div class="flex items-center justify-between">
            <code class="text-sm font-mono text-gray-900 dark:text-gray-100">
              {{ generatedApiKey }}
            </code>
            <button
              @click="copyApiKey"
              class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300"
            >
              <Copy class="w-4 h-4" />
            </button>
          </div>
        </div>

        <div class="flex justify-end">
          <button
            @click="showGeneratedKey = false"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors"
          >
            I've Copied It
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { 
  Save, Mail, Shield, Upload, Settings, Key, Plus, Trash2, Copy 
} from 'lucide-vue-next'
import axios from 'axios'

// Reactive data
const activeSection = ref('general')
const saving = ref(false)
const testingEmail = ref(false)
const showCreateApiKey = ref(false)
const showGeneratedKey = ref(false)
const creatingApiKey = ref(false)
const generatedApiKey = ref('')

const settings = ref({
  general: {
    siteName: '',
    siteUrl: '',
    siteDescription: '',
    adminEmail: '',
    timezone: 'UTC',
    registrationEnabled: true,
    maintenanceMode: false
  },
  email: {
    smtpHost: '',
    smtpPort: 587,
    fromEmail: '',
    fromName: '',
    notifications: {
      newUser: true,
      imageUpload: false,
      contentReport: true
    }
  },
  security: {
    sessionTimeout: 120,
    maxLoginAttempts: 5,
    lockoutDuration: 15,
    passwordMinLength: 8,
    requireEmailVerification: true,
    enable2FA: false,
    forceHttps: true,
    enableCaptcha: true
  },
  uploads: {
    maxFileSize: 10,
    maxImagesPerUser: 1000,
    allowedTypes: 'jpg,jpeg,png,gif,webp',
    autoOptimize: true,
    generateThumbnails: true,
    requireModeration: false
  }
})

const apiKeys = ref([])

const newApiKey = ref({
  name: '',
  description: ''
})

const sections = [
  { key: 'general', name: 'General', icon: Settings },
  { key: 'email', name: 'Email', icon: Mail },
  { key: 'security', name: 'Security', icon: Shield },
  { key: 'uploads', name: 'Uploads', icon: Upload }
]

// Methods
const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString()
}

const fetchSettings = async () => {
  try {
    const response = await axios.get('/api/v1/admin/settings')
    settings.value = { ...settings.value, ...response.data.data }
  } catch (error) {
    console.error('Failed to fetch settings:', error)
  }
}

const saveSettings = async () => {
  saving.value = true
  
  try {
    await axios.put('/api/v1/admin/settings', settings.value)
    // Show success notification
    console.log('Settings saved successfully')
  } catch (error) {
    console.error('Failed to save settings:', error)
    // Show error notification
  } finally {
    saving.value = false
  }
}

const testEmail = async () => {
  testingEmail.value = true
  
  try {
    await axios.post('/api/v1/admin/settings/test-email')
    // Show success notification
    console.log('Test email sent successfully')
  } catch (error) {
    console.error('Failed to send test email:', error)
    // Show error notification
  } finally {
    testingEmail.value = false
  }
}

const fetchApiKeys = async () => {
  try {
    const response = await axios.get('/api/v1/admin/api-keys')
    apiKeys.value = response.data.data
  } catch (error) {
    console.error('Failed to fetch API keys:', error)
  }
}

const createApiKey = async () => {
  creatingApiKey.value = true
  
  try {
    const response = await axios.post('/api/v1/admin/api-keys', newApiKey.value)
    
    // Add to list
    apiKeys.value.unshift(response.data.data)
    
    // Show the generated key
    generatedApiKey.value = response.data.data.key
    showGeneratedKey.value = true
    
    // Reset form
    showCreateApiKey.value = false
    newApiKey.value = { name: '', description: '' }
  } catch (error) {
    console.error('Failed to create API key:', error)
  } finally {
    creatingApiKey.value = false
  }
}

const revokeApiKey = async (id) => {
  if (!confirm('Are you sure you want to revoke this API key? This action cannot be undone.')) {
    return
  }
  
  try {
    await axios.delete(`/api/v1/admin/api-keys/${id}`)
    
    // Update local state
    const keyIndex = apiKeys.value.findIndex(key => key.id === id)
    if (keyIndex > -1) {
      apiKeys.value[keyIndex].status = 'revoked'
    }
  } catch (error) {
    console.error('Failed to revoke API key:', error)
  }
}

const copyApiKey = async () => {
  try {
    await navigator.clipboard.writeText(generatedApiKey.value)
    // Show success notification
    console.log('API key copied to clipboard')
  } catch (error) {
    console.error('Failed to copy API key:', error)
  }
}

// Lifecycle
onMounted(() => {
  fetchSettings()
  fetchApiKeys()
})
</script>