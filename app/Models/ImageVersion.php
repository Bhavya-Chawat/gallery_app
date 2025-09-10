<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ImageVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'image_id',
        'variant',
        'format',
        'storage_path',
        'mime_type',
        'width',
        'height',
        'size_bytes',
        'quality',
        'is_generated',
        'generated_at',
    ];

    protected $casts = [
        'width' => 'integer',
        'height' => 'integer',
        'size_bytes' => 'integer',
        'quality' => 'integer',
        'is_generated' => 'boolean',
        'generated_at' => 'datetime',
    ];

    // Relationships
    public function image(): BelongsTo
    {
        return $this->belongsTo(Image::class);
    }

    // Methods
    public function getUrl(): string
    {
        return Storage::disk('s3')->url($this->storage_path);
    }

    public function getSignedUrl(int $ttl = 300): string
    {
        return Storage::disk('s3')->temporaryUrl($this->storage_path, now()->addSeconds($ttl));
    }

    public function markAsGenerated(): void
    {
        $this->update([
            'is_generated' => true,
            'generated_at' => now(),
        ]);
    }

    public function getFormattedSizeAttribute(): string
    {
        if ($this->size_bytes === 0) return '0 B';
        
        $units = ['B', 'KB', 'MB', 'GB'];
        $unitIndex = floor(log($this->size_bytes, 1024));
        
        return round($this->size_bytes / pow(1024, $unitIndex), 2) . ' ' . $units[$unitIndex];
    }

    public function getDimensionsAttribute(): string
    {
        return "{$this->width} × {$this->height}";
    }

    // Scopes
    public function scopeGenerated($query)
    {
        return $query->where('is_generated', true);
    }

    public function scopeByVariant($query, string $variant)
    {
        return $query->where('variant', $variant);
    }

    public function scopeByFormat($query, string $format)
    {
        return $query->where('format', $format);
    }
}
