<template>
  <AdminLayout>
    <div class="min-h-screen bg-gray-50 dark:bg-slate-900">
      <!-- Header -->
      <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl border-b border-gray-200 dark:border-slate-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                Content Management
              </h1>
              <p class="text-gray-500 dark:text-slate-400 mt-1">
                Manage images, albums, collections, and tags across the platform
              </p>
            </div>
            
            <div class="flex items-center space-x-3">
              <!-- Bulk Actions Dropdown -->
              <div class="relative">
                <button
                  @click="showBulkActions = !showBulkActions"
                  :disabled="selectedItems.length === 0"
                  class="bg-yellow-600 hover:bg-yellow-700 disabled:opacity-50 disabled:cursor-not-allowed text-white px-4 py-2 rounded-lg transition-colors"
                >
                  <Edit class="w-4 h-4 inline mr-2" />
                  Bulk Actions ({{ selectedItems.length }})
                  <ChevronDown class="w-4 h-4 inline ml-1" />
                </button>
                
                <!-- Bulk Actions Menu -->
                <div 
                  v-if="showBulkActions"
                  class="absolute right-0 mt-2 w-48 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg shadow-lg py-2 z-50"
                >
                  <button
                    @click="bulkApprove"
                    class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700"
                  >
                    <Check class="w-4 h-4 inline mr-2" />
                    Approve Selected
                  </button>
                  <button
                    @click="bulkFeature"
                    class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700"
                  >
                    <Star class="w-4 h-4 inline mr-2" />
                    Feature Selected
                  </button>
                  <button
                    @click="bulkHide"
                    class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700"
                  >
                    <EyeOff class="w-4 h-4 inline mr-2" />
                    Hide Selected
                  </button>
                  <hr class="my-1 border-gray-200 dark:border-slate-700" />
                  <button
                    @click="bulkDelete"
                    class="w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-slate-700"
                  >
                    <Trash2 class="w-4 h-4 inline mr-2" />
                    Delete Selected
                  </button>
                </div>
              </div>
              
              <button
                @click="showCreateCollection = true"
                class="bg-gradient-to-r from-blue-600 via-cyan-500 to-blue-400 text-white px-6 py-2 rounded-lg hover:shadow-lg transition-all duration-200 font-medium"
              >
                <Plus class="w-4 h-4 inline mr-2" />
                Create Collection
              </button>
            </div>
          </div>
        </div>
      </div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl rounded-xl p-6 border border-gray-200 dark:border-slate-700">
            <div class="flex items-center">
              <div class="p-3 rounded-lg bg-blue-100 dark:bg-blue-900/30">
                <Image class="w-6 h-6 text-blue-600 dark:text-blue-400" />
              </div>
              <div class="ml-4">
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                  {{ contentStats.totalImages?.toLocaleString() || 0 }}
                </p>
                <p class="text-gray-500 dark:text-slate-400">Total Images</p>
              </div>
            </div>
          </div>

          <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl rounded-xl p-6 border border-gray-200 dark:border-slate-700">
            <div class="flex items-center">
              <div class="p-3 rounded-lg bg-green-100 dark:bg-green-900/30">
                <Folder class="w-6 h-6 text-green-600 dark:text-green-400" />
              </div>
              <div class="ml-4">
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                  {{ contentStats.totalAlbums || 0 }}
                </p>
                <p class="text-gray-500 dark:text-slate-400">Albums</p>
              </div>
            </div>
          </div>

          <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl rounded-xl p-6 border border-gray-200 dark:border-slate-700">
            <div class="flex items-center">
              <div class="p-3 rounded-lg bg-purple-100 dark:bg-purple-900/30">
                <Layers class="w-6 h-6 text-purple-600 dark:text-purple-400" />
              </div>
              <div class="ml-4">
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                  {{ contentStats.totalCollections || 0 }}
                </p>
                <p class="text-gray-500 dark:text-slate-400">Collections</p>
              </div>
            </div>
          </div>

          <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl rounded-xl p-6 border border-gray-200 dark:border-slate-700">
            <div class="flex items-center">
              <div class="p-3 rounded-lg bg-orange-100 dark:bg-orange-900/30">
                <Clock class="w-6 h-6 text-orange-600 dark:text-orange-400" />
              </div>
              <div class="ml-4">
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                  {{ contentStats.pendingReview || 0 }}
                </p>
                <p class="text-gray-500 dark:text-slate-400">Pending Review</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Filter Tabs -->
        <div class="mb-6">
          <nav class="flex space-x-8">
            <button
              v-for="tab in tabs"
              :key="tab.key"
              @click="activeTab = tab.key"
              :class="{
                'border-blue-500 text-blue-600 dark:text-blue-400': activeTab === tab.key,
                'border-transparent text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-300': activeTab !== tab.key
              }"
              class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors"
            >
              {{ tab.name }}
            </button>
          </nav>
        </div>

        <!-- Filters and Search -->
        <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl rounded-xl border border-gray-200 dark:border-slate-700 p-4 mb-6">
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
            <div class="flex items-center space-x-4">
              <select 
                v-model="filters.status"
                @change="fetchContent"
                class="px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 text-sm"
              >
                <option value="">All Status</option>
                <option value="published">Published</option>
                <option value="draft">Draft</option>
                <option value="pending">Pending Review</option>
                <option value="featured">Featured</option>
                <option value="hidden">Hidden</option>
              </select>
              
              <select 
                v-model="filters.sort"
                @change="fetchContent"
                class="px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 text-sm"
              >
                <option value="newest">Newest First</option>
                <option value="oldest">Oldest First</option>
                <option value="most_viewed">Most Viewed</option>
                <option value="most_liked">Most Liked</option>
                <option value="title">Alphabetical</option>
              </select>
              
              <input 
                v-model="filters.search"
                @input="debounceSearch"
                type="text"
                placeholder="Search content..."
                class="px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 text-sm"
              />
            </div>
            
            <div class="flex items-center space-x-2">
              <button
                @click="viewMode = 'grid'"
                :class="{
                  'bg-blue-600 text-white': viewMode === 'grid',
                  'bg-white dark:bg-slate-700 text-gray-700 dark:text-slate-300': viewMode !== 'grid'
                }"
                class="p-2 border border-gray-300 dark:border-slate-600 rounded-lg transition-colors"
              >
                <Grid class="w-4 h-4" />
              </button>
              <button
                @click="viewMode = 'list'"
                :class="{
                  'bg-blue-600 text-white': viewMode === 'list',
                  'bg-white dark:bg-slate-700 text-gray-700 dark:text-slate-300': viewMode !== 'list'
                }"
                class="p-2 border border-gray-300 dark:border-slate-600 rounded-lg transition-colors"
              >
                <List class="w-4 h-4" />
              </button>
            </div>
          </div>
        </div>

        <!-- Content Grid/List -->
        <div v-if="activeTab === 'images'">
          <!-- Grid View -->
          <div v-if="viewMode === 'grid'" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <div
              v-for="image in images"
              :key="image.id"
              class="group relative aspect-square rounded-lg overflow-hidden cursor-pointer"
              @click="selectItem(image.id)"
            >
              <!-- Selection Overlay -->
              <div 
                v-if="selectedItems.includes(image.id)"
                class="absolute inset-0 bg-blue-500/20 border-2 border-blue-500 z-10"
              >
                <div class="absolute top-2 left-2 bg-blue-500 text-white rounded-full p-1">
                  <Check class="w-3 h-3" />
                </div>
              </div>
              
              <!-- Image -->
              <img 
                :src="image.thumbnail_url" 
                :alt="image.title"
                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
              />
              
              <!-- Status Badge -->
              <div class="absolute top-2 right-2">
                <span 
                  class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                  :class="{
                    'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400': image.status === 'published',
                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400': image.status === 'pending',
                    'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400': image.status === 'featured',
                    'bg-gray-100 text-gray-800 dark:bg-slate-600 dark:text-slate-300': image.status === 'hidden'
                  }"
                >
                  {{ image.status }}
                </span>
              </div>
              
              <!-- Info Overlay -->
              <div class="absolute inset-0 bg-black/0 group-hover:bg-black/50 transition-all duration-200 flex items-end">
                <div class="w-full p-3 text-white transform translate-y-full group-hover:translate-y-0 transition-transform duration-200">
                  <p class="text-sm font-medium truncate">{{ image.title }}</p>
                  <p class="text-xs opacity-90">{{ image.views }} views • {{ image.likes }} likes</p>
                </div>
              </div>
            </div>
          </div>
          
          <!-- List View -->
          <div v-else class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl rounded-xl border border-gray-200 dark:border-slate-700">
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead class="bg-gray-50 dark:bg-slate-700/50">
                  <tr>
                    <th class="w-12 px-4 py-3">
                      <input
                        type="checkbox"
                        @change="toggleSelectAll"
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                      />
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                      Image
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                      Status
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                      Views
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                      Likes
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                      Uploaded
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-slate-700">
                  <tr 
                    v-for="image in images" 
                    :key="image.id"
                    class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors"
                  >
                    <td class="px-4 py-4">
                      <input
                        v-model="selectedItems"
                        :value="image.id"
                        type="checkbox"
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                      />
                    </td>
                    <td class="px-6 py-4">
                      <div class="flex items-center">
                        <div class="flex-shrink-0 h-12 w-12">
                          <img 
                            :src="image.thumbnail_url" 
                            :alt="image.title"
                            class="h-12 w-12 rounded-lg object-cover"
                          />
                        </div>
                        <div class="ml-4">
                          <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                            {{ image.title }}
                          </div>
                          <div class="text-sm text-gray-500 dark:text-slate-400">
                            by {{ image.user?.name }}
                          </div>
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4">
                      <select 
                        :value="image.status"
                        @change="updateItemStatus(image.id, $event.target.value)"
                        class="text-sm border border-gray-300 dark:border-slate-600 rounded px-2 py-1 bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100"
                      >
                        <option value="published">Published</option>
                        <option value="pending">Pending</option>
                        <option value="featured">Featured</option>
                        <option value="hidden">Hidden</option>
                      </select>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                      {{ image.views?.toLocaleString() || 0 }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                      {{ image.likes || 0 }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-slate-400">
                      {{ formatDate(image.created_at) }}
                    </td>
                    <td class="px-6 py-4 text-right text-sm font-medium">
                      <div class="flex items-center justify-end space-x-2">
                        <button
                          @click="editItem(image)"
                          class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300"
                        >
                          <Edit class="w-4 h-4" />
                        </button>
                        <button
                          @click="viewItem(image.id)"
                          class="text-gray-600 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-300"
                        >
                          <ExternalLink class="w-4 h-4" />
                        </button>
                        <button
                          @click="deleteItem(image.id)"
                          class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300"
                        >
                          <Trash2 class="w-4 h-4" />
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Albums Tab -->
        <div v-if="activeTab === 'albums'" class="space-y-6">
          <div 
            v-for="album in albums" 
            :key="album.id"
            class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl rounded-xl border border-gray-200 dark:border-slate-700 p-6"
          >
            <div class="flex items-start space-x-4">
              <input
                v-model="selectedItems"
                :value="album.id"
                type="checkbox"
                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mt-1"
              />

              <!-- Album Cover -->
              <div class="flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden bg-gray-200 dark:bg-slate-600">
                <img 
                  v-if="album.cover_image_url"
                  :src="album.cover_image_url" 
                  :alt="album.title"
                  class="w-full h-full object-cover"
                />
                <div v-else class="w-full h-full flex items-center justify-center">
                  <Folder class="w-8 h-8 text-gray-400 dark:text-slate-500" />
                </div>
              </div>

              <div class="flex-grow">
                <div class="flex items-start justify-between">
                  <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                      {{ album.title }}
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">
                      {{ album.images_count }} images • Created {{ formatDate(album.created_at) }}
                    </p>
                    <p v-if="album.description" class="text-sm text-gray-600 dark:text-slate-400 mt-2">
                      {{ album.description }}
                    </p>
                  </div>
                  
                  <div class="flex items-center space-x-2">
                    <span 
                      class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                      :class="{
                        'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400': album.privacy === 'public',
                        'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400': album.privacy === 'unlisted',
                        'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400': album.privacy === 'private'
                      }"
                    >
                      {{ album.privacy }}
                    </span>
                    
                    <div class="flex space-x-1">
                      <button
                        @click="editItem(album)"
                        class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 p-1"
                      >
                        <Edit class="w-4 h-4" />
                      </button>
                      <button
                        @click="viewItem(album.id, 'album')"
                        class="text-gray-600 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-300 p-1"
                      >
                        <ExternalLink class="w-4 h-4" />
                      </button>
                      <button
                        @click="deleteItem(album.id)"
                        class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 p-1"
                      >
                        <Trash2 class="w-4 h-4" />
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Collections Tab -->
        <div v-if="activeTab === 'collections'" class="space-y-6">
          <div 
            v-for="collection in collections" 
            :key="collection.id"
            class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl rounded-xl border border-gray-200 dark:border-slate-700 p-6"
          >
            <div class="flex items-start space-x-4">
              <input
                v-model="selectedItems"
                :value="collection.id"
                type="checkbox"
                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mt-1"
              />

              <div class="flex-grow">
                <div class="flex items-start justify-between">
                  <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                      {{ collection.title }}
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">
                      {{ collection.images_count }} items • Curated by {{ collection.curator?.name }}
                    </p>
                    <p v-if="collection.description" class="text-sm text-gray-600 dark:text-slate-400 mt-2">
                      {{ collection.description }}
                    </p>
                  </div>
                  
                  <div class="flex items-center space-x-2">
                    <span 
                      v-if="collection.is_featured"
                      class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400"
                    >
                      Featured
                    </span>
                    
                    <div class="flex space-x-1">
                      <button
                        @click="editItem(collection)"
                        class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 p-1"
                      >
                        <Edit class="w-4 h-4" />
                      </button>
                      <button
                        @click="viewItem(collection.id, 'collection')"
                        class="text-gray-600 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-300 p-1"
                      >
                        <ExternalLink class="w-4 h-4" />
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="pagination" class="mt-8 flex items-center justify-between">
          <div class="text-sm text-gray-700 dark:text-slate-300">
            Showing {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }} results
          </div>
          <div class="flex space-x-2">
            <button
              v-for="page in paginationPages"
              :key="page"
              @click="changePage(page)"
              :disabled="page === pagination.current_page || page === '...'"
              class="px-3 py-1 text-sm rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
              :class="{
                'bg-blue-600 text-white': page === pagination.current_page,
                'bg-white dark:bg-slate-700 text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-600': page !== pagination.current_page && page !== '...'
              }"
            >
              {{ page }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Create Collection Modal -->
    <div 
      v-if="showCreateCollection"
      class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50"
      @click.self="showCreateCollection = false"
    >
      <div class="bg-white dark:bg-slate-800 rounded-xl p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
          Create New Collection
        </h3>
        
        <form @submit.prevent="createCollection" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
              Title
            </label>
            <input
              v-model="newCollection.title"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
              Description
            </label>
            <textarea
              v-model="newCollection.description"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            ></textarea>
          </div>

          <div class="flex items-center">
            <input
              v-model="newCollection.is_featured"
              type="checkbox"
              class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
            />
            <label class="ml-2 text-sm text-gray-700 dark:text-slate-300">
              Feature this collection
            </label>
          </div>

          <div class="flex justify-end space-x-3 pt-4">
            <button
              type="button"
              @click="showCreateCollection = false"
              class="text-gray-600 dark:text-slate-400 hover:text-gray-800 dark:hover:text-slate-200"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="creatingCollection"
              class="bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed text-white px-4 py-2 rounded-lg transition-colors"
            >
              {{ creatingCollection ? 'Creating...' : 'Create Collection' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { 
  Edit, ChevronDown, Check, Star, EyeOff, Trash2, Plus, Image, Folder, 
  Layers, Clock, Grid, List, ExternalLink
} from 'lucide-vue-next'
import axios from 'axios'

// Reactive data
const activeTab = ref('images')
const viewMode = ref('grid')
const selectedItems = ref([])
const showBulkActions = ref(false)
const showCreateCollection = ref(false)

const contentStats = ref({
  totalImages: 0,
  totalAlbums: 0,
  totalCollections: 0,
  pendingReview: 0
})

const filters = ref({
  status: '',
  sort: 'newest',
  search: ''
})

const images = ref([])
const albums = ref([])
const collections = ref([])
const pagination = ref(null)

const newCollection = ref({
  title: '',
  description: '',
  is_featured: false
})

const creatingCollection = ref(false)
const searchTimeout = ref(null)

const tabs = [
  { key: 'images', name: 'Images' },
  { key: 'albums', name: 'Albums' },
  { key: 'collections', name: 'Collections' }
]

// Computed
const paginationPages = computed(() => {
  if (!pagination.value) return []
  
  const pages = []
  const current = pagination.value.current_page
  const last = pagination.value.last_page
  
  if (current > 3) pages.push(1)
  if (current > 4) pages.push('...')
  
  for (let i = Math.max(1, current - 2); i <= Math.min(last, current + 2); i++) {
    pages.push(i)
  }
  
  if (current < last - 3) pages.push('...')
  if (current < last - 2) pages.push(last)
  
  return pages
})

// Methods
const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString()
}

const fetchContentStats = async () => {
  try {
    const response = await axios.get('/api/v1/admin/content/stats')
    contentStats.value = response.data.data
  } catch (error) {
    console.error('Failed to fetch content stats:', error)
  }
}

const fetchContent = async (page = 1) => {
  try {
    const endpoint = activeTab.value === 'images' ? 'images' : 
                    activeTab.value === 'albums' ? 'albums' : 'collections'
    
    const params = new URLSearchParams({
      page,
      ...filters.value
    })
    
    const response = await axios.get(`/api/v1/admin/content/${endpoint}?${params}`)
    
    if (activeTab.value === 'images') {
      images.value = response.data.data
    } else if (activeTab.value === 'albums') {
      albums.value = response.data.data
    } else {
      collections.value = response.data.data
    }
    
    pagination.value = response.data.meta
  } catch (error) {
    console.error('Failed to fetch content:', error)
  }
}

const debounceSearch = () => {
  clearTimeout(searchTimeout.value)
  searchTimeout.value = setTimeout(() => {
    fetchContent()
  }, 500)
}

const selectItem = (id) => {
  const index = selectedItems.value.indexOf(id)
  if (index > -1) {
    selectedItems.value.splice(index, 1)
  } else {
    selectedItems.value.push(id)
  }
}

const toggleSelectAll = (event) => {
  if (event.target.checked) {
    const currentItems = activeTab.value === 'images' ? images.value : 
                         activeTab.value === 'albums' ? albums.value : collections.value
    selectedItems.value = currentItems.map(item => item.id)
  } else {
    selectedItems.value = []
  }
}

const updateItemStatus = async (id, status) => {
  try {
    const endpoint = activeTab.value === 'images' ? 'images' : 
                     activeTab.value === 'albums' ? 'albums' : 'collections'
    
    await axios.put(`/api/v1/admin/content/${endpoint}/${id}`, { status })
    
    // Update local state
    const items = activeTab.value === 'images' ? images.value : 
                  activeTab.value === 'albums' ? albums.value : collections.value
    const item = items.find(i => i.id === id)
    if (item) {
      item.status = status
    }
  } catch (error) {
    console.error('Failed to update item status:', error)
  }
}

const bulkApprove = async () => {
  try {
    const endpoint = activeTab.value === 'images' ? 'images' : 
                     activeTab.value === 'albums' ? 'albums' : 'collections'
    
    await axios.post(`/api/v1/admin/content/${endpoint}/bulk-approve`, {
      ids: selectedItems.value
    })
    
    selectedItems.value = []
    showBulkActions.value = false
    await fetchContent()
  } catch (error) {
    console.error('Failed to bulk approve:', error)
  }
}

const bulkFeature = async () => {
  try {
    const endpoint = activeTab.value === 'images' ? 'images' : 
                     activeTab.value === 'albums' ? 'albums' : 'collections'
    
    await axios.post(`/api/v1/admin/content/${endpoint}/bulk-feature`, {
      ids: selectedItems.value
    })
    
    selectedItems.value = []
    showBulkActions.value = false
    await fetchContent()
  } catch (error) {
    console.error('Failed to bulk feature:', error)
  }
}

const bulkHide = async () => {
  try {
    const endpoint = activeTab.value === 'images' ? 'images' : 
                     activeTab.value === 'albums' ? 'albums' : 'collections'
    
    await axios.post(`/api/v1/admin/content/${endpoint}/bulk-hide`, {
      ids: selectedItems.value
    })
    
    selectedItems.value = []
    showBulkActions.value = false
    await fetchContent()
  } catch (error) {
    console.error('Failed to bulk hide:', error)
  }
}

const bulkDelete = async () => {
  if (!confirm(`Are you sure you want to delete ${selectedItems.value.length} items?`)) return
  
  try {
    const endpoint = activeTab.value === 'images' ? 'images' : 
                     activeTab.value === 'albums' ? 'albums' : 'collections'
    
    await axios.post(`/api/v1/admin/content/${endpoint}/bulk-delete`, {
      ids: selectedItems.value
    })
    
    selectedItems.value = []
    showBulkActions.value = false
    await fetchContent()
  } catch (error) {
    console.error('Failed to bulk delete:', error)
  }
}

const createCollection = async () => {
  creatingCollection.value = true
  
  try {
    const response = await axios.post('/api/v1/admin/collections', newCollection.value)
    collections.value.unshift(response.data.data)
    
    showCreateCollection.value = false
    newCollection.value = { title: '', description: '', is_featured: false }
  } catch (error) {
    console.error('Failed to create collection:', error)
  } finally {
    creatingCollection.value = false
  }
}

const editItem = (item) => {
  // Open edit modal or navigate to edit page
  const type = activeTab.value === 'images' ? 'image' : 
               activeTab.value === 'albums' ? 'album' : 'collection'
  console.log(`Edit ${type}:`, item)
}

const viewItem = (id, type = 'image') => {
  const routes = {
    image: `/gallery/image/${id}`,
    album: `/albums/${id}`,
    collection: `/collections/${id}`
  }
  window.open(routes[type] || routes.image, '_blank')
}

const deleteItem = async (id) => {
  if (!confirm('Are you sure you want to delete this item?')) return
  
  try {
    const endpoint = activeTab.value === 'images' ? 'images' : 
                     activeTab.value === 'albums' ? 'albums' : 'collections'
    
    await axios.delete(`/api/v1/admin/content/${endpoint}/${id}`)
    await fetchContent()
  } catch (error) {
    console.error('Failed to delete item:', error)
  }
}

const changePage = (page) => {
  if (page !== '...' && page !== pagination.value?.current_page) {
    fetchContent(page)
  }
}

// Methods for handling tab changes
const handleTabChange = () => {
  selectedItems.value = []
  fetchContent()
}

// Lifecycle
onMounted(() => {
  fetchContentStats()
  fetchContent()
})

// Watch activeTab for changes
watch(activeTab, handleTabChange)
</script>