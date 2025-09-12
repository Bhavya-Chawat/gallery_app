<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Image extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'owner_id',
        'album_id',
        'title',
        'caption',
        'alt_text',
        'slug',
        'original_filename',
        'storage_path',
        'mime_type',
        'size_bytes',
        'width',
        'height',
        'aspect_ratio',
        'privacy',
        'license',
        'exif_data',
        'processing_status',
        'taken_at',
        'camera_make',
        'camera_model',
        'views_count',
        'likes_count',
        'comments_count',
        'is_published',
        'published_at',
        'allow_comments',
        'allow_downloads',
    ];

    protected $casts = [
        'size_bytes' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'aspect_ratio' => 'float',
        'exif_data' => 'array',
        'processing_status' => 'array',
        'taken_at' => 'datetime',
        'views_count' => 'integer',
        'likes_count' => 'integer',
        'comments_count' => 'integer',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'allow_comments' => 'boolean',
        'allow_downloads' => 'boolean',
    ];

    // Relationships
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }

    public function versions(): HasMany
    {
        return $this->hasMany(ImageVersion::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->where('status', 'approved');
    }

    public function allComments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function viewCounts(): MorphMany
    {
        return $this->morphMany(ViewCount::class, 'viewable');
    }

    public function auditLogs(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($image) {
            if (empty($image->slug)) {
                $baseSlug = $image->title ? Str::slug($image->title) : Str::slug(pathinfo($image->original_filename, PATHINFO_FILENAME));
                $image->slug = $baseSlug;
                
                // Ensure uniqueness
                $originalSlug = $image->slug;
                $counter = 1;
                while (static::where('slug', $image->slug)->exists()) {
                    $image->slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }

            // Calculate aspect ratio
            if ($image->width && $image->height) {
                $image->aspect_ratio = $image->width / $image->height;
            }
        });

        static::updating(function ($image) {
            // Recalculate aspect ratio if dimensions change
            if (($image->isDirty('width') || $image->isDirty('height')) && $image->width && $image->height) {
                $image->aspect_ratio = $image->width / $image->height;
            }
        });
    }

    // URL Methods
    public function getUrl(string $variant = 'original', string $format = null): string
    {
        if ($variant === 'original') {
            return $this->getOriginalUrl();
        }

        $version = $this->versions()
            ->where('variant', $variant)
            ->when($format, fn($q) => $q->where('format', $format))
            ->first();

        if (!$version) {
            // Fallback to original if variant doesn't exist
            return $this->getOriginalUrl();
        }

        return Storage::disk('minio')->url($version->storage_path);
    }

    public function getOriginalUrl(): string
    {
        return Storage::disk('minio')->url($this->storage_path);
    }

    public function getSignedUrl(string $variant = "original", int $ttl = 300): string
    {
        $path = $variant === "original" 
            ? $this->storage_path 
            : $this->versions()->where("variant", $variant)->first()?->storage_path ?? $this->storage_path;
        
        // Get signed URL and fix internal Docker hostname
        $url = Storage::disk("minio")->temporaryUrl($path, now()->addSeconds($ttl));
        return str_replace("minio:9000", "localhost:9000", $url);
    }

        public function collections()
    {
        return $this->belongsToMany(Collection::class, 'collection_items', 'collectable_id', 'collection_id')
            ->where('collection_items.collectable_type', self::class)
            ->withTimestamps();
    }



    // Status Methods
    public function isPublic(): bool
    {
        return $this->privacy === 'public' && $this->is_published;
    }

    public function isVisible(): bool
    {
        return in_array($this->privacy, ['public', 'unlisted']) && $this->is_published;
    }

    public function isProcessed(): bool
    {
        $status = $this->processing_status ?? [];
        return ($status['thumbnails_generated'] ?? false) && ($status['metadata_extracted'] ?? false);
    }

    public function publish(): void
    {
        $this->update([
            'is_published' => true,
            'published_at' => now(),
        ]);
    }

    public function unpublish(): void
    {
        $this->update([
            'is_published' => false,
            'published_at' => null,
        ]);
    }

    // Counter Methods
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    public function incrementLikes(): void
    {
        $this->increment('likes_count');
    }

    public function decrementLikes(): void
    {
        $this->decrement('likes_count');
    }

    public function incrementComments(): void
    {
        $this->increment('comments_count');
    }

    public function decrementComments(): void
    {
        $this->decrement('comments_count');
    }

    // Utility Methods
    public function getFormattedSizeAttribute(): string
    {
        return $this->formatBytes($this->size_bytes);
    }

    public function getDimensionsAttribute(): string
    {
        return "{$this->width} × {$this->height}";
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes === 0) return '0 B';
        
        $units = ['B', 'KB', 'MB', 'GB'];
        $unitIndex = floor(log($bytes, 1024));
        
        return round($bytes / pow(1024, $unitIndex), 2) . ' ' . $units[$unitIndex];
    }

    // Scopes
    public function scopePublic($query)
    {
        return $query->where('privacy', 'public')
                    ->where('is_published', true);
    }

    public function scopeVisible($query)
    {
        return $query->whereIn('privacy', ['public', 'unlisted'])
                    ->where('is_published', true);
    }

    public function scopeByOwner($query, int $ownerId)
    {
        return $query->where('owner_id', $ownerId);
    }

    public function scopeInAlbum($query, string $albumId)
    {
        return $query->where('album_id', $albumId);
    }

    public function scopeWithTag($query, string $tagSlug)
    {
        return $query->whereHas('tags', function ($q) use ($tagSlug) {
            $q->where('slug', $tagSlug);
        });
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeProcessed($query)
    {
        return $query->whereJsonContains('processing_status->thumbnails_generated', true)
                    ->whereJsonContains('processing_status->metadata_extracted', true);
    }
}
