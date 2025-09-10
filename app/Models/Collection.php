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
        'items_count',
        'is_published',
        'published_at',
        'metadata',
    ];

    protected $casts = [
        'items_count' => 'integer',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'metadata' => 'array',
    ];

    // Relationships
    public function curator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'curator_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(CollectionItem::class)->orderBy('sort_order');
    }

    public function coverImage(): BelongsTo
    {
        return $this->belongsTo(Image::class, 'cover_image_id');
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

    public function addItem(Model $item, string $description = null, int $sortOrder = null): CollectionItem
    {
        if ($sortOrder === null) {
            $sortOrder = $this->items()->max('sort_order') + 1 ?? 0;
        }

        $collectionItem = $this->items()->create([
            'collectable_type' => get_class($item),
            'collectable_id' => $item->id,
            'description' => $description,
            'sort_order' => $sortOrder,
        ]);

        $this->updateItemsCount();
        
        return $collectionItem;
    }

    public function removeItem(Model $item): bool
    {
        $removed = $this->items()
            ->where('collectable_type', get_class($item))
            ->where('collectable_id', $item->id)
            ->delete();

        if ($removed) {
            $this->updateItemsCount();
            return true;
        }

        return false;
    }

    public function reorderItems(array $itemIds): void
    {
        foreach ($itemIds as $index => $itemId) {
            $this->items()->where('id', $itemId)->update(['sort_order' => $index]);
        }
    }

    private function updateItemsCount(): void
    {
        $this->update(['items_count' => $this->items()->count()]);
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

    public function scopeByCurator($query, int $curatorId)
    {
        return $query->where('curator_id', $curatorId);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}
