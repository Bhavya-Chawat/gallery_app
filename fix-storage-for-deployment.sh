#!/bin/bash

echo "🔧 FIXING STORAGE FOR DEPLOYMENT"
echo "================================"

# Step 1: Create ImageService (Backend)
echo "📁 Creating ImageService..."
mkdir -p app/Services
cat > app/Services/ImageService.php << 'PHPEOF'
<?php

namespace App\Services;

class ImageService
{
    public static function getImageUrl($imagePath)
    {
        if (!$imagePath) {
            return '/images/placeholder.jpg';
        }

        // Check if we're in local development
        if (config('app.env') === 'local' && config('filesystems.default') === 's3') {
            // Local development with MinIO
            $endpoint = config('filesystems.disks.s3.endpoint', 'http://localhost:9000');
            $bucket = config('filesystems.disks.s3.bucket', 'gallery-images');
            return "{$endpoint}/{$bucket}/{$imagePath}";
        } else {
            // Production with public storage
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
PHPEOF

# Step 2: Update User Model
echo "👤 Updating User model..."
# Add avatar_url accessor to User model
sed -i '/protected $appends/d' app/Models/User.php
sed -i '/use Laravel\\Sanctum\\HasApiTokens;/a use App\\Services\\ImageService;' app/Models/User.php
sed -i '/protected $casts = \[/i\    protected $appends = ['\''avatar_url'\''];' app/Models/User.php
sed -i '/protected $casts = \[/a\\n    public function getAvatarUrlAttribute()\n    {\n        return ImageService::getAvatarUrl($this);\n    }' app/Models/User.php

# Step 3: Create Universal Image Helper (JavaScript)
echo "🎨 Creating universal image helper..."
mkdir -p resources/js/utils
cat > resources/js/utils/imageHelpers.js << 'JSEOF'
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
JSEOF

# Step 4: Add helper to main layout
echo "📄 Adding helper to main layout..."
if ! grep -q "imageHelpers.js" resources/views/app.blade.php 2>/dev/null; then
    sed -i '/<head>/a\    <script src="{{ asset('\''js/utils/imageHelpers.js'\'') }}" defer></script>' resources/views/app.blade.php 2>/dev/null || echo "Layout file not found, skipping"
fi

# Step 5: Update ProfileController for flexible storage
echo "🎛️ Updating ProfileController..."
cat > app/Http/Controllers/ProfileController.php << 'PHPEOF'
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
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            'user' => $request->user(),
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
        
        // Delete old avatar if exists
        if ($user->avatar_path) {
            Storage::disk(config('filesystems.default'))->delete($user->avatar_path);
        }
        
        // Store new avatar
        $path = $request->file('avatar')->store('avatars', config('filesystems.default'));
        
        // Update user
        $user->update(['avatar_path' => $path]);
        
        return redirect()->back()->with('success', 'Avatar updated successfully!');
    }
}
PHPEOF

# Step 6: Create simple Railway config
echo "🚂 Creating Railway config..."
cat > nixpacks.toml << 'RAILWAYEOF'
[start]
cmd = 'php artisan serve --host=0.0.0.0 --port=$PORT'
RAILWAYEOF

# Step 7: Create production environment template
echo "🌍 Creating production env template..."
cat > .env.production << 'ENVEOF'
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app.up.railway.app

DB_CONNECTION=pgsql
DB_HOST=${PGHOST}
DB_PORT=${PGPORT}
DB_DATABASE=${PGDATABASE}
DB_USERNAME=${PGUSER}
DB_PASSWORD=${PGPASSWORD}

FILESYSTEM_DISK=public
SESSION_DRIVER=database
CACHE_DRIVER=database
QUEUE_CONNECTION=database
ENVEOF

# Step 8: Check routes
echo "🛣️ Ensuring routes exist..."
if ! grep -q "profile/avatar" routes/web.php; then
    echo "Adding avatar route..."
    echo "Route::post('/profile/avatar', [App\Http\Controllers\ProfileController::class, 'uploadAvatar'])->name('profile.avatar')->middleware('auth');" >> routes/web.php
fi

echo ""
echo "✅ SETUP COMPLETE!"
echo ""
echo "🧪 Test locally first:"
echo "1. Visit: http://localhost"
echo "2. Try uploading an avatar"
echo "3. Check that images load"
echo ""
echo "🚀 For Railway deployment:"
echo "1. git add ."
echo "2. git commit -m 'Fix storage for deployment'"
echo "3. git push origin main"
echo "4. Deploy on Railway with these variables:"
echo "   APP_ENV=production"
echo "   APP_DEBUG=false"
echo "   FILESYSTEM_DISK=public"
echo "   SESSION_DRIVER=database"
echo "   CACHE_DRIVER=database"
echo ""
echo "💡 Your app will automatically use:"
echo "   - MinIO locally (localhost:9000)"
echo "   - Public storage on Railway (/storage)"
