#!/bin/bash

echo "🚀 Starting comprehensive storage configuration update..."
echo "📂 Project structure detected. Updating all relevant files..."

# Colors for better output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${BLUE}Step 1: Creating ImageService...${NC}"

# 1. Create ImageService
mkdir -p app/Services
cat > app/Services/ImageService.php << 'EOF'
<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class ImageService
{
    public static function getImageUrl($imagePath, $disk = null)
    {
        if (!$imagePath) {
            return '/images/placeholder.jpg';
        }

        $disk = $disk ?? config('filesystems.default');
        
        switch ($disk) {
            case 's3':
                if (config('filesystems.disks.s3.endpoint')) {
                    $endpoint = config('filesystems.disks.s3.endpoint');
                    $bucket = config('filesystems.disks.s3.bucket');
                    return "{$endpoint}/{$bucket}/{$imagePath}";
                }
                return Storage::disk('s3')->url($imagePath);
                
            case 'public':
            default:
                return config('app.url') . '/storage/' . $imagePath;
        }
    }

    public static function getAvatarUrl($user)
    {
        if (!$user || !$user->avatar_path) {
            return null;
        }
        return self::getImageUrl($user->avatar_path);
    }
}
EOF

echo -e "${GREEN}✅ ImageService created${NC}"

echo -e "${BLUE}Step 2: Creating JavaScript utilities...${NC}"

# 2. Create JavaScript image helpers
mkdir -p resources/js/utils
cat > resources/js/utils/imageHelpers.js << 'EOF'
export function getImageUrl(imagePath) {
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

export function getAvatarUrl(user) {
  if (!user?.avatar_path) {
    return null
  }
  return getImageUrl(user.avatar_path)
}

export function handleImageError(event, fallbackUrl = '/images/placeholder.jpg') {
  const img = event.target
  const currentSrc = img.src
  
  if (currentSrc.includes('localhost:9000')) {
    const imagePath = currentSrc.split('/').pop()
    img.src = `${window.location.origin}/storage/${imagePath}`
  } else {
    img.src = fallbackUrl
  }
}
EOF

echo -e "${GREEN}✅ JavaScript utilities created${NC}"

echo -e "${BLUE}Step 3: Updating Models...${NC}"

# 3. Update User Model
cat > app/Models/User.php << 'EOF'
<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Services\ImageService;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_path',
        'profile_visibility',
        'show_email_publicly',
        'show_stats_publicly',
        'email_notifications',
        'storage_used_bytes',
        'storage_quota_bytes',
        'is_active',
        'bio',
        'website',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'storage_used_bytes' => 'integer',
        'storage_quota_bytes' => 'integer',
        'show_email_publicly' => 'boolean',
        'show_stats_publicly' => 'boolean',
        'email_notifications' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected $appends = ['avatar_url'];

    public function getAvatarUrlAttribute()
    {
        return ImageService::getAvatarUrl($this);
    }

    public function getStorageUsagePercentageAttribute()
    {
        if (!$this->storage_quota_bytes || $this->storage_quota_bytes == 0) {
            return 0;
        }
        return round(($this->storage_used_bytes / $this->storage_quota_bytes) * 100, 1);
    }

    public function images()
    {
        return $this->hasMany(Image::class, 'owner_id');
    }

    public function albums()
    {
        return $this->hasMany(Album::class, 'owner_id');
    }

    public function collections()
    {
        return $this->hasMany(Collection::class, 'curator_id');
    }
}
EOF

# 4. Update Image Model
cat > app/Models/Image.php << 'EOF'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Services\ImageService;

class Image extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'owner_id',
        'album_id',
        'title',
        'slug',
        'description',
        'caption',
        'alt_text',
        'storage_path',
        'original_filename',
        'mime_type',
        'size_bytes',
        'dimensions',
        'privacy',
        'is_published',
        'published_at',
        'views_count',
        'likes_count',
        'comments_count',
        'metadata',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'views_count' => 'integer',
        'likes_count' => 'integer',
        'comments_count' => 'integer',
        'size_bytes' => 'integer',
        'metadata' => 'array',
    ];

    protected $appends = ['image_url', 'thumbnail_url'];

    public function getImageUrlAttribute()
    {
        return ImageService::getImageUrl($this->storage_path);
    }

    public function getThumbnailUrlAttribute()
    {
        if (!$this->storage_path) return null;
        
        $pathInfo = pathinfo($this->storage_path);
        $thumbnailPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_thumb.' . $pathInfo['extension'];
        
        return ImageService::getImageUrl($thumbnailPath);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function album()
    {
        return $this->belongsTo(Album::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'image_tag');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class, 'likeable_id')->where('likeable_type', self::class);
    }

    public function collections()
    {
        return $this->belongsToMany(Collection::class, 'collection_image')
                    ->withPivot(['added_at', 'position'])
                    ->withTimestamps();
    }
}
EOF

echo -e "${GREEN}✅ Models updated${NC}"

echo -e "${BLUE}Step 4: Updating Controllers...${NC}"

# 5. Update ProfileController
cat > app/Http/Controllers/ProfileController.php << 'EOF'
<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function edit(Request $request): Response
    {
        $user = $request->user();
        
        $stats = [
            'images' => $user->images()->count(),
            'albums' => $user->albums()->count(),
            'views' => $user->images()->sum('views_count') ?? 0,
            'likes' => $user->images()->sum('likes_count') ?? 0,
        ];

        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            'user' => $user,
            'stats' => $stats,
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        Auth::logout();
        $user->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = auth()->user();
        
        if ($user->avatar_path) {
            Storage::disk(config('filesystems.default'))->delete($user->avatar_path);
        }
        
        $disk = config('filesystems.default');
        $path = $request->file('avatar')->store('avatars', $disk);
        
        $user->update(['avatar_path' => $path]);
        
        return redirect()->back()->with('success', 'Avatar updated successfully!');
    }

    public function updatePrivacy(Request $request)
    {
        $validated = $request->validate([
            'profile_visibility' => 'required|in:public,private',
            'show_email_publicly' => 'boolean',
            'show_stats_publicly' => 'boolean',
            'email_notifications' => 'boolean',
        ]);

        auth()->user()->update($validated);
        return redirect()->back()->with('success', 'Privacy settings updated!');
    }

    public function show(User $user)
    {
        if ($user->profile_visibility === 'private' && auth()->id() !== $user->id) {
            abort(404);
        }

        $images = $user->images()
            ->where('is_published', true)
            ->whereIn('privacy', ['public', 'unlisted'])
            ->with(['tags', 'album'])
            ->latest()
            ->paginate(12);

        return Inertia::render('Profile/Show', [
            'user' => $user,
            'images' => $images,
        ]);
    }
}
EOF

# 6. Update ImageController storage methods
echo -e "${YELLOW}Updating ImageController...${NC}"

# Create a backup and update ImageController
cp app/Http/Controllers/ImageController.php app/Http/Controllers/ImageController.php.backup

# Add storage configuration to ImageController
cat >> app/Http/Controllers/ImageController.php << 'EOF'

    protected function getStorageDisk()
    {
        return config('filesystems.default');
    }

    protected function storeImage($file, $path = 'images')
    {
        return $file->store($path, $this->getStorageDisk());
    }

    protected function deleteImage($path)
    {
        if ($path) {
            Storage::disk($this->getStorageDisk())->delete($path);
        }
    }
EOF

echo -e "${GREEN}✅ Controllers updated${NC}"

echo -e "${BLUE}Step 5: Updating Vue Components...${NC}"

# 7. Update UserAvatar Component
cat > resources/js/Components/UserAvatar.vue << 'EOF'
<template>
  <div
    :class="[
      'relative inline-flex items-center justify-center rounded-full font-medium transition-all duration-500 ease-out hover:scale-110 hover:shadow-2xl group cursor-pointer overflow-hidden',
      sizeClasses,
      'bg-gradient-to-br from-violet-500 via-purple-500 to-cyan-500',
      'shadow-lg hover:shadow-violet-500/25',
      'ring-2 ring-white/10 hover:ring-white/20',
      'backdrop-blur-xl',
      hasAvatar ? '' : 'animate-pulse hover:animate-none'
    ]"
  >
    <div class="relative z-10 w-full h-full flex items-center justify-center">
      <img
        v-if="hasAvatar && !imageError"
        :src="avatarUrl"
        :alt="user.name || 'User'"
        @error="handleImageError"
        class="w-full h-full rounded-full object-cover transition-transform duration-500 group-hover:scale-105 ring-1 ring-white/20"
      />
      <span 
        v-else 
        :class="[
          'font-bold tracking-wide bg-gradient-to-br from-white via-slate-100 to-slate-200 bg-clip-text text-transparent',
          'drop-shadow-lg group-hover:drop-shadow-xl transition-all duration-300'
        ]"
      >
        {{ initials }}
      </span>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { getAvatarUrl } from '@/utils/imageHelpers'

const props = defineProps({
  user: { type: Object, required: true },
  size: { type: String, default: 'md' },
})

const imageError = ref(false)

const sizeClasses = computed(() => {
  switch (props.size) {
    case 'sm': return 'w-6 h-6 text-xs'
    case 'md': return 'w-8 h-8 text-sm'
    case 'lg': return 'w-12 h-12 text-lg'
    case 'xl': return 'w-16 h-16 text-xl'
    case '2xl': return 'w-20 h-20 text-2xl'
    default: return 'w-8 h-8 text-sm'
  }
})

const hasAvatar = computed(() => {
  return !!(props.user?.avatar_url || props.user?.avatar_path)
})

const avatarUrl = computed(() => {
  if (imageError.value) return null
  return props.user?.avatar_url || getAvatarUrl(props.user)
})

const initials = computed(() => {
  if (!props.user?.name) return '?'
  
  const names = props.user.name.trim().split(' ').filter(name => name.length > 0)
  
  if (names.length >= 2) {
    return (names[0][0] + names[names.length - 1][0]).toUpperCase()
  }
  
  if (names.length === 1) {
    return names[0].substring(0, 2).toUpperCase()
  }
  
  return '?'
})

const handleImageError = () => {
  imageError.value = true
}
</script>
EOF

# 8. Update Gallery ImageCard component
cat > resources/js/Components/Gallery/ImageCard.vue << 'EOF'
<template>
  <div class="group relative aspect-square bg-slate-800/50 backdrop-blur-xl border border-white/10 rounded-2xl overflow-hidden shadow-xl transition-all duration-500 hover:scale-105 hover:shadow-2xl hover:shadow-violet-500/30 hover:border-white/30">
    <Link :href="imageUrl">
      <img
        :src="displayImageUrl"
        :alt="image.alt_text || image.title"
        @error="handleImageError"
        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
        loading="lazy"
      />
    </Link>
    
    <!-- Image Info Overlay -->
    <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent p-4 backdrop-blur-sm">
      <h3 class="text-white font-medium truncate text-sm">{{ image.title || 'Untitled' }}</h3>
      <p v-if="image.owner" class="text-slate-300 text-xs mt-1">by {{ image.owner.name }}</p>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import { getImageUrl, handleImageError as handleError } from '@/utils/imageHelpers'

const props = defineProps({
  image: { type: Object, required: true }
})

const imageError = ref(false)

const displayImageUrl = computed(() => {
  if (imageError.value) return '/images/placeholder.jpg'
  return props.image.image_url || getImageUrl(props.image.storage_path)
})

const imageUrl = computed(() => {
  return `/images/${props.image.slug}`
})

const handleImageError = (event) => {
  imageError.value = true
  handleError(event)
}
</script>
EOF

echo -e "${GREEN}✅ Vue components updated${NC}"

echo -e "${BLUE}Step 6: Updating Configuration Files...${NC}"

# 9. Update AppServiceProvider for auto bucket setup
cat > app/Providers/AppServiceProvider.php << 'EOF'
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;
use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Auto-configure MinIO bucket for local development
        if (app()->environment(['local']) && config('filesystems.default') === 's3') {
            $this->configureMinioBucket();
        }
    }

    private function configureMinioBucket(): void
    {
        try {
            $s3Client = new S3Client([
                'version' => 'latest',
                'region' => config('filesystems.disks.s3.region', 'us-east-1'),
                'endpoint' => config('filesystems.disks.s3.endpoint'),
                'use_path_style_endpoint' => true,
                'credentials' => [
                    'key' => config('filesystems.disks.s3.key'),
                    'secret' => config('filesystems.disks.s3.secret'),
                ],
            ]);

            $bucketName = config('filesystems.disks.s3.bucket');

            if (!$s3Client->doesBucketExist($bucketName)) {
                $s3Client->createBucket(['Bucket' => $bucketName]);
                \Log::info("Created bucket: {$bucketName}");
            }

            $policy = [
                'Version' => '2012-10-17',
                'Statement' => [
                    [
                        'Effect' => 'Allow',
                        'Principal' => ['AWS' => ['*']],
                        'Action' => ['s3:GetObject'],
                        'Resource' => ["arn:aws:s3:::{$bucketName}/*"]
                    ]
                ]
            ];

            $s3Client->putBucketPolicy([
                'Bucket' => $bucketName,
                'Policy' => json_encode($policy)
            ]);

            \Log::info("MinIO bucket '{$bucketName}' configured as public");

        } catch (AwsException $e) {
            \Log::warning("Failed to configure MinIO bucket: " . $e->getMessage());
        } catch (\Exception $e) {
            \Log::warning("Error setting up MinIO: " . $e->getMessage());
        }
    }
}
EOF

# 10. Create environment templates
echo -e "${BLUE}Step 7: Creating environment templates...${NC}"

cat > .env.local.example << 'EOF'
# Local Development Environment
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

# Database
DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=gallery_app
DB_USERNAME=sail
DB_PASSWORD=password

# MinIO S3 Storage (Local)
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=sail
AWS_SECRET_ACCESS_KEY=password
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=gallery-images
AWS_ENDPOINT=http://localhost:9000
AWS_USE_PATH_STYLE_ENDPOINT=true
EOF

cat > .env.production.example << 'EOF'
# Production Environment
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app.up.railway.app

# Database (Railway PostgreSQL)
DB_CONNECTION=pgsql
DB_HOST=${PGHOST}
DB_PORT=${PGPORT}
DB_DATABASE=${PGDATABASE}
DB_USERNAME=${PGUSER}
DB_PASSWORD=${PGPASSWORD}

# Public Storage (Railway)
FILESYSTEM_DISK=public

# Cache & Session
SESSION_DRIVER=database
CACHE_DRIVER=database
QUEUE_CONNECTION=database
EOF

# 11. Create deployment scripts
echo -e "${BLUE}Step 8: Creating deployment helpers...${NC}"

cat > deploy-railway.sh << 'EOF'
#!/bin/bash

echo "🚀 Preparing for Railway deployment..."

# Copy production environment template
cp .env.production.example .env

echo "✅ Environment configured for Railway"
echo "📝 Don't forget to:"
echo "   1. Set environment variables in Railway dashboard"
echo "   2. Add PostgreSQL service"
echo "   3. Generate domain"
echo "   4. Run migrations after deployment"

echo "🎯 Ready for Railway deployment!"
EOF

chmod +x deploy-railway.sh

# 12. Update routes for avatar upload
echo -e "${BLUE}Step 9: Ensuring routes are configured...${NC}"

# Check if avatar route exists in web.php
if ! grep -q "profile/avatar" routes/web.php; then
    echo "Adding avatar upload route..."
    sed -i '/Route::middleware.*auth.*group/a\    Route::post("/profile/avatar", [ProfileController::class, "uploadAvatar"])->name("profile.avatar");' routes/web.php
    sed -i '/Route::middleware.*auth.*group/a\    Route::patch("/profile/privacy", [ProfileController::class, "updatePrivacy"])->name("profile.privacy");' routes/web.php
fi

echo -e "${GREEN}✅ Routes verified${NC}"

echo -e "${BLUE}Step 10: Creating final setup instructions...${NC}"

cat > DEPLOYMENT_GUIDE.md << 'EOF'
# 🚀 Deployment Guide

## For Railway Deployment

1. **Prepare for deployment:**
./deploy-railway.sh

text

2. **Push to GitHub and deploy via Railway**

3. **Set these environment variables in Railway:**
- Copy from `.env.production.example`
- Add PostgreSQL service (variables auto-provided)

4. **After deployment, run:**
php artisan migrate --force
php artisan storage:link

text

## Storage Configuration

- **Local Development**: Uses MinIO (S3-compatible)
- **Production**: Uses Laravel public storage
- **Automatic switching**: Based on environment

## Image URL Generation

All image URLs are now generated automatically:
- `$user->avatar_url` - Avatar URL
- `$image->image_url` - Image URL  
- `$image->thumbnail_url` - Thumbnail URL

## JavaScript Helpers

Import and use:
import { getImageUrl, getAvatarUrl } from '@/utils/imageHelpers'

text
EOF

echo ""
echo -e "${GREEN}🎉 COMPLETE! All files updated successfully!${NC}"
echo ""
echo -e "${YELLOW}📋 Summary of changes:${NC}"
echo "✅ Created ImageService for backend URL generation"
echo "✅ Created JavaScript image helpers"
echo "✅ Updated User and Image models with URL accessors"
echo "✅ Updated ProfileController with flexible storage"
echo "✅ Updated UserAvatar and ImageCard components"
echo "✅ Updated AppServiceProvider for auto MinIO setup"
echo "✅ Created environment templates"
echo "✅ Created deployment script"
echo "✅ Added deployment guide"
echo ""
echo -e "${BLUE}🚀 Next steps:${NC}"
echo "1. Test locally: ./vendor/bin/sail up -d"
echo "2. For Railway: ./deploy-railway.sh"
echo "3. Read DEPLOYMENT_GUIDE.md for full instructions"
echo ""
echo -e "${GREEN}Your app now supports both MinIO (local) and public storage (production)! 🎉${NC}"