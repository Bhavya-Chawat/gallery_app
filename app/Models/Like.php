<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Like extends Model
{
    use HasFactory;

    protected $fillable = [
        'likeable_type',
        'likeable_id',
        'user_id',
        'user_ip',
    ];

    // Relationships
    public function likeable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::created(function ($like) {
            // Increment the likes counter on the likeable model
            if (method_exists($like->likeable, 'incrementLikes')) {
                $like->likeable->incrementLikes();
            }
        });

        static::deleted(function ($like) {
            // Decrement the likes counter on the likeable model
            if (method_exists($like->likeable, 'decrementLikes')) {
                $like->likeable->decrementLikes();
            }
        });
    }

    // Scopes
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForType($query, string $type)
    {
        return $query->where('likeable_type', $type);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}
