<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been set up for each driver as an example of the required values.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', true),
            'throw' => false,
            'visibility' => 'public',
            'options' => [
                'ACL' => 'public-read',
                'override_visibility_on_copy' => true,
            ],
        ],

       // MinIO Configuration (S3-compatible)
        'minio' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID', 'sail'),
            'secret' => env('AWS_SECRET_ACCESS_KEY', 'password'),
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'bucket' => env('AWS_BUCKET', 'local'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT', 'http://minio:9000'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', true),
            'throw' => false,
            'options' => [
                'CacheControl' => 'max-age=31536000', // 1 year cache
                'Metadata' => [
                    'uploaded-by' => 'gallery-app',
                ],
            ],
            // Custom visibility settings for gallery images
            'visibility' => 'public',
        ],

        // Temporary storage for uploads
        'temp' => [
            'driver' => 'local',
            'root' => storage_path('app/temp'),
            'throw' => false,
        ],

        // Backup storage
        'backups' => [
            'driver' => 'local',
            'root' => storage_path('app/backups'),
            'throw' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Gallery Specific Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration specific to the image gallery application
    |
    */

    'gallery' => [
        // Maximum file size in bytes (50MB)
        'max_upload_size' => env('GALLERY_MAX_UPLOAD_SIZE', 52428800),
        
        // Allowed MIME types
        'allowed_mimes' => explode(',', env('GALLERY_ALLOWED_MIMES', 'image/jpeg,image/png,image/webp,image/avif')),
        
        // Allowed file extensions
        'allowed_extensions' => explode(',', env('GALLERY_ALLOWED_EXTENSIONS', 'jpg,jpeg,png,webp,avif')),
        
        // Image processing settings
        'thumbnails' => [
            'thumb' => ['width' => 200, 'height' => 200, 'fit' => 'crop'],
            'small' => ['width' => 640, 'height' => null, 'fit' => 'max'],
            'medium' => ['width' => 1280, 'height' => null, 'fit' => 'max'],
            'large' => ['width' => 1920, 'height' => null, 'fit' => 'max'],
        ],
        
        // Image quality settings
        'quality' => [
            'jpeg' => env('GALLERY_QUALITY_JPEG', 85),
            'webp' => env('GALLERY_QUALITY_WEBP', 80),
            'avif' => env('GALLERY_QUALITY_AVIF', 70),
        ],
        
        // Presigned URL TTL (15 minutes)
        'presign_ttl' => env('GALLERY_PRESIGN_TTL', 900),
        
        // Download URL TTL (5 minutes)
        'download_ttl' => env('GALLERY_DOWNLOAD_TTL', 300),
        
        // Storage paths
        'paths' => [
            'images' => 'images/{year}/{month}',
            'thumbnails' => 'thumbnails/{year}/{month}',
            'avatars' => 'avatars',
            'temp' => 'temp/{session}',
        ],
    ],

];
