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
