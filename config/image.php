<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Image Driver
    |--------------------------------------------------------------------------
    |
    | Intervention Image supports "GD Library" and "Imagick" to process images
    | internally. Depending on your PHP setup, you can choose one of them.
    |
    | Included options:
    |   - \Intervention\Image\Drivers\Gd\Driver::class
    |   - \Intervention\Image\Drivers\Imagick\Driver::class
    |
    */

    'driver' => \Intervention\Image\Drivers\Gd\Driver::class,

    /*
    |--------------------------------------------------------------------------
    | Image Processing Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for image processing operations
    |
    */

    'processing' => [
        // Maximum image dimensions for processing
        'max_width' => 4000,
        'max_height' => 4000,
        
        // Memory limit for image processing (in MB)
        'memory_limit' => 256,
        
        // Timeout for image processing (in seconds)
        'timeout' => 30,
        
        // Auto-orient images based on EXIF data
        'auto_orient' => true,
        
        // Strip EXIF data (except safe metadata)
        'strip_exif' => true,
        
        // Preserve color profiles
        'preserve_color_profile' => false,
        
        // Progressive JPEG encoding
        'progressive_jpeg' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Watermark Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for applying watermarks to images
    |
    */

    'watermark' => [
        'enabled' => env('GALLERY_WATERMARK_ENABLED', false),
        'image' => storage_path('app/watermark.png'),
        'position' => 'bottom-right',
        'opacity' => 50,
        'margin' => 10,
    ],

    /*
    |--------------------------------------------------------------------------
    | EXIF Data Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for handling EXIF metadata
    |
    */

    'exif' => [
        // Extract and store EXIF data
        'extract' => true,
        
        // Remove GPS coordinates for privacy
        'remove_gps' => true,
        
        // Allowed EXIF tags to preserve
        'allowed_tags' => [
            'Make',
            'Model',
            'DateTime',
            'DateTimeOriginal',
            'Orientation',
            'XResolution',
            'YResolution',
            'ResolutionUnit',
            'Software',
            'ExposureTime',
            'FNumber',
            'ISO',
            'FocalLength',
            'Flash',
            'WhiteBalance',
        ],
    ],

];