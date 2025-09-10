<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ViewCount extends Model
{
    use HasFactory;

    protected $fillable = [
        'viewable_type',
        'viewable_id',
        'date',
        'count',
        'metadata',
    ];

    protected $casts = [
        'date' => 'date',
        'count' => 'integer',
        'metadata' => 'array',
    ];

    // Relationships
    public function viewable(): MorphTo
    {
        return $this->morphTo();
    }

    // Methods
    public static function recordView(Model $viewable, array $metadata = []): void
    {
        static::updateOrCreate(
            [
                'viewable_type' => get_class($viewable),
                'viewable_id' => $viewable->id,
                'date' => now()->toDateString(),
            ],
            [
                'count' => \DB::raw('count + 1'),
                'metadata' => $metadata,
            ]
        );
    }

    // Scopes
    public function scopeForType($query, string $type)
    {
        return $query->where('viewable_type', $type);
    }

    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    public function scopeToday($query)
    {
        return $query->where('date', now()->toDateString());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('date', [
            now()->startOfWeek()->toDateString(),
            now()->endOfWeek()->toDateString(),
        ]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereBetween('date', [
            now()->startOfMonth()->toDateString(),
            now()->endOfMonth()->toDateString(),
        ]);
    }
}
