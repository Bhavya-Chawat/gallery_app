<?php

namespace App\Jobs;

use App\Models\Image;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image as InterventionImage;

class ProcessImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $image;

    public function __construct(Image $image)
    {
        $this->image = $image;
    }

    public function handle()
    {
        try {
            // Get image from storage
            $disk = Storage::disk(config('filesystems.default'));
            $imagePath = $this->image->storage_path;
            
            if (!$disk->exists($imagePath)) {
                throw new \Exception("Image file not found: {$imagePath}");
            }

            // Get image contents and create Intervention Image
            $imageContents = $disk->get($imagePath);
            $img = InterventionImage::make($imageContents);

            // Extract dimensions
            $width = $img->width();
            $height = $img->height();
            $aspectRatio = $height > 0 ? round($width / $height, 4) : 1.0;

            // Update image record
            $this->image->update([
                'width' => $width,
                'height' => $height,
                'aspect_ratio' => $aspectRatio,
                'processing_status' => [
                    'thumbnails_generated' => true,
                    'metadata_extracted' => true,
                ],
            ]);

            // Generate thumbnails (optional)
            $this->generateThumbnails($img, $disk);

        } catch (\Exception $e) {
            \Log::error('ProcessImageJob failed', [
                'image_id' => $this->image->id,
                'error' => $e->getMessage(),
            ]);
            
            // Mark as failed
            $this->image->update([
                'processing_status' => [
                    'thumbnails_generated' => false,
                    'metadata_extracted' => false,
                    'error' => $e->getMessage(),
                ],
            ]);
        }
    }

    private function generateThumbnails($img, $disk)
    {
        $imageId = $this->image->id;
        $basePath = dirname($this->image->storage_path);
        
        $sizes = [
            'thumb' => 150,
            'small' => 400,
            'medium' => 800,
        ];

        foreach ($sizes as $variant => $maxSize) {
            $resized = clone $img;
            $resized->resize($maxSize, $maxSize, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            $thumbnailPath = "{$basePath}/{$variant}.jpg";
            $disk->put($thumbnailPath, $resized->encode('jpg', 85));

            // Optionally save thumbnail info to database
            // You might have an ImageVersion model for this
        }
    }
}
