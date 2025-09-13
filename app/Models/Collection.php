<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Collection extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'curator_id',
        'title',
        'slug',
        'description',
        'privacy',
        'cover_image_id',
        'images_count',
        'is_published',
        'published_at',
        'metadata',
    ];

    protected $casts = [
        'images_count' => 'integer',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'metadata' => 'array',
    ];

    // FIXED: Route key name method moved to top for clarity
    public function getRouteKeyName()
    {
        return 'slug';
    }

    // Relationships
    public function curator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'curator_id');
    }

    // FIXED: Updated images relationship with better pivot ordering
    public function images(): BelongsToMany
    {
        return $this->belongsToMany(Image::class, 'collection_image')
            ->withPivot(['added_at', 'position'])
            ->withTimestamps()
            ->orderByPivot('added_at', 'desc'); // Changed from pivot_added_at to added_at
    }

    public function coverImage(): BelongsTo
    {
        return $this->belongsTo(Image::class, 'cover_image_id');
    }

    // FIXED: Improved boot method with better slug handling
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($collection) {
            if (empty($collection->slug)) {
                $collection->slug = Str::slug($collection->title);
                
                // Ensure uniqueness
                $originalSlug = $collection->slug;
                $counter = 1;
                while (static::where('slug', $collection->slug)->exists()) {
                    $collection->slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }

            // FIXED: Initialize images_count to 0 if not set
            if (is_null($collection->images_count)) {
                $collection->images_count = 0;
            }
        });

        // FIXED: Improved updating logic
        static::updating(function ($collection) {
            if ($collection->isDirty('title')) {
                $newSlug = Str::slug($collection->title);
                
                // Only update slug if it's different and ensure uniqueness
                if ($newSlug !== $collection->slug) {
                    $originalSlug = $newSlug;
                    $counter = 1;
                    while (static::where('slug', $newSlug)->where('id', '!=', $collection->id)->exists()) {
                        $newSlug = $originalSlug . '-' . $counter;
                        $counter++;
                    }
                    $collection->slug = $newSlug;
                }
            }
        });
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
        return $query->where('curator_id', $ownerId);
    }

    // FIXED: Added scope for user's own collections (including private)
    public function scopeOwnedBy($query, int $userId)
    {
        return $query->where('curator_id', $userId);
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

    // FIXED: Added method to check if user can view this collection
    public function canBeViewedBy($user = null): bool
    {
        if ($this->privacy === 'public' && $this->is_published) {
            return true;
        }

        if ($this->privacy === 'unlisted' && $this->is_published) {
            return true;
        }

        if ($user && $this->curator_id === $user->id) {
            return true;
        }

        return false;
    }

    // FIXED: Added method to check if user can edit this collection
    public function canBeEditedBy($user = null): bool
    {
        if (!$user) {
            return false;
        }

        return $this->curator_id === $user->id;
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

    // FIXED: Improved updateImageCount method with automatic cover image assignment
    public function updateImageCount(): void
    {
        $count = $this->images()->count();
        $this->update(['images_count' => $count]);

        // FIXED: Automatically set cover image if none exists and we have images
        if (!$this->cover_image_id && $count > 0) {
            $firstImage = $this->images()->first();
            if ($firstImage) {
                $this->update(['cover_image_id' => $firstImage->id]);
            }
        }

        // FIXED: Remove cover image if no images left
        if ($this->cover_image_id && $count === 0) {
            $this->update(['cover_image_id' => null]);
        }
    }

    // FIXED: Added helper method to add image with proper pivot data
    public function addImage(Image $image, array $pivotData = []): bool
    {
        // Check if already exists
        if ($this->images()->where('image_id', $image->id)->exists()) {
            return false;
        }

        // Add with default pivot data
        $defaultPivotData = [
            'added_at' => now(),
            'position' => $this->images()->count() + 1,
        ];

        $this->images()->attach($image->id, array_merge($defaultPivotData, $pivotData));

        // Update count and cover image
        $this->increment('images_count');

        if (!$this->cover_image_id) {
            $this->update(['cover_image_id' => $image->id]);
        }

        return true;
    }

    // FIXED: Added helper method to remove image
    public function removeImage(Image $image): bool
    {
        $removed = $this->images()->detach($image->id);

        if ($removed) {
            $this->decrement('images_count');

            // Update cover image if this was the cover
            if ($this->cover_image_id === $image->id) {
                $newCover = $this->images()->first();
                $this->update(['cover_image_id' => $newCover?->id]);
            }

            return true;
        }

        return false;
    }

    // FIXED: Added method to get formatted image count
    public function getFormattedImagesCountAttribute(): string
    {
        $count = $this->images_count ?? 0;
        
        if ($count === 0) return 'No images';
        if ($count === 1) return '1 image';
        return number_format($count) . ' images';
    }

    // FIXED: Added accessor for better date formatting
    public function getFormattedCreatedAtAttribute(): string
    {
        return $this->created_at ? $this->created_at->format('M j, Y') : 'Unknown';
    }

    public function getFormattedUpdatedAtAttribute(): string
    {
        return $this->updated_at ? $this->updated_at->format('M j, Y') : 'Unknown';
    }
}
