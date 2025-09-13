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
