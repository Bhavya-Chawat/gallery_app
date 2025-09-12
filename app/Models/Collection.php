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

    // Relationships
    public function curator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'curator_id');
    }

    public function images(): BelongsToMany
    {
        return $this->belongsToMany(Image::class, 'collection_image') // Use collection_image table
            ->withPivot(['added_at', 'position'])
            ->withTimestamps()
            ->orderBy('pivot_added_at', 'desc');
    }

    public function coverImage(): BelongsTo
    {
        return $this->belongsTo(Image::class, 'cover_image_id');
    }

    // Boot method for auto-generating slug
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
        });

        static::updating(function ($collection) {
            if ($collection->isDirty('title') && empty($collection->getOriginal('slug'))) {
                $collection->slug = Str::slug($collection->title);
            }
        });
    }
public function getRouteKeyName()
{
    return 'slug';
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
        $this->update(['images_count' => $count]);
    }
}
