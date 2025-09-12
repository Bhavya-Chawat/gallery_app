<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_path',
        'bio',
        'website',
        'social_links',
        'profile_visibility',
        'show_email_publicly',
        'show_stats_publicly',
        'storage_used_bytes',
        'storage_quota_bytes',
        'preferences',
        'last_login_at',
        'is_active',
        'email_notifications',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'preferences' => 'array',
        'social_links' => 'array',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
        'email_notifications' => 'boolean',
        'show_email_publicly' => 'boolean',
        'show_stats_publicly' => 'boolean',
        'storage_used_bytes' => 'integer',
        'storage_quota_bytes' => 'integer',
    ];

    // Relationships
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    public function albums(): HasMany
    {
        return $this->hasMany(Album::class, 'owner_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(Image::class, 'owner_id');
    }

    public function collections(): HasMany
    {
        return $this->hasMany(Collection::class, 'curator_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    // Role and Permission Methods
    public function hasRole(string $role): bool
    {
        return $this->roles()->where('slug', $role)->exists();
    }

    public function hasAnyRole(array $roles): bool
    {
        return $this->roles()->whereIn('slug', $roles)->exists();
    }

    public function hasPermission(string $permission): bool
    {
        return $this->roles->contains(function ($role) use ($permission) {
            return $role->hasPermission($permission);
        });
    }

    public function assignRole(string $roleSlug): void
    {
        $role = Role::where('slug', $roleSlug)->firstOrFail();
        if (!$this->roles()->where('role_id', $role->id)->exists()) {
            $this->roles()->attach($role, ['assigned_at' => now()]);
        }
    }

    public function removeRole(string $roleSlug): void
    {
        $role = Role::where('slug', $roleSlug)->first();
        if ($role) {
            $this->roles()->detach($role);
        }
    }

    // Storage Methods (CLEANED - NO DUPLICATES)
    public function getRemainingStorageBytes(): int
    {
        $quota = $this->storage_quota_bytes ?? 1073741824; // 1GB default
        $used = $this->storage_used_bytes ?? 0;
        return max(0, $quota - $used);
    }

    public function getStorageUsagePercentage(): float
    {
        $quota = $this->storage_quota_bytes ?? 1073741824;
        if ($quota === 0) {
            return 0;
        }
        $used = $this->storage_used_bytes ?? 0;
        return ($used / $quota) * 100;
    }

    public function canUpload(int $fileSizeBytes): bool
    {
        return $this->getRemainingStorageBytes() >= $fileSizeBytes;
    }

    public function incrementStorageUsage(int $bytes): void
    {
        $this->increment('storage_used_bytes', $bytes);
    }

    public function decrementStorageUsage(int $bytes): void
    {
        $this->decrement('storage_used_bytes', max(0, $bytes));
    }

    // Utility Methods
    public function getAvatarUrl(): ?string
    {
        return $this->avatar_path ? "http://localhost:9000/gallery-images/{$this->avatar_path}" : null;
    }

    public function updateLastLogin(): void
    {
        $this->update(['last_login_at' => now()]);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    public function scopeWithRole($query, string $role)
    {
        return $query->whereHas('roles', function ($q) use ($role) {
            $q->where('slug', $role);
        });
    }
}
