<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Album extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'owner_id',
        'title',
        'slug',
        'description',
        'privacy',
        'cover_image_id',
        'image_order',
        'image_count',
        'total_size_bytes',
        'is_published',
        'published_at',
        'metadata',
    ];

    protected $casts = [
        'image_order' => 'array',
        'image_count' => 'integer',
        'total_size_bytes' => 'integer',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'metadata' => 'array',
    ];

    // Relationships
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(Image::class)->orderBy('created_at', 'desc');
    }

    public function coverImage(): BelongsTo
    {
        return $this->belongsTo(Image::class, 'cover_image_id');
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

    // Boot method for auto-generating slug
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($album) {
            if (empty($album->slug)) {
                $album->slug = Str::slug($album->title);
                
                // Ensure uniqueness
                $originalSlug = $album->slug;
                $counter = 1;
                while (static::where('slug', $album->slug)->exists()) {
                    $album->slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }
        });

        static::updating(function ($album) {
            if ($album->isDirty('title') && empty($album->getOriginal('slug'))) {
                $album->slug = Str::slug($album->title);
            }
        });
    }

    // Methods
    public function isPublic(): bool
    {
        return $this->privacy === 'public' && $this->is_published;
    }

    public function isVisible(): bool
    {
        return in_array($this->privacy, ['public', 'unlisted']) && $this->is_published;
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

    public function updateImageCount(): void
    {
        $count = $this->images()->count();
        $totalSize = $this->images()->sum('size_bytes');
        
        $this->update([
            'image_count' => $count,
            'total_size_bytes' => $totalSize,
        ]);
    }

    public function reorderImages(array $imageIds): void
    {
        $this->update(['image_order' => $imageIds]);
    }

    public function getFormattedSizeAttribute(): string
    {
        return $this->formatBytes($this->total_size_bytes);
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes === 0) return '0 B';
        
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
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

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    
/**
 * Get the album's cover image or first image
 */
public function getCoverImageAttribute()
{
    if ($this->cover_image_id && $this->coverImage) {
        return $this->coverImage;
    }
    
    // Return first image as fallback
    return $this->images()->first();
}

/**
 * Get optimized thumbnail URL
 */
public function getThumbnailUrlAttribute()
{
    $coverImage = $this->cover_image;
    
    if ($coverImage && $coverImage->storage_path) {
        return "http://localhost:9000/gallery-images/{$coverImage->storage_path}";
    }
    
    return '/images/album-placeholder.jpg';
}

/**
 * Auto-assign cover image when adding first image
 */
public function autoAssignCoverImage()
{
    if (!$this->cover_image_id && $this->images()->count() > 0) {
        $firstImage = $this->images()->orderBy('created_at', 'asc')->first();
        if ($firstImage) {
            $this->update(['cover_image_id' => $firstImage->id]);
        }
    }
}


}
