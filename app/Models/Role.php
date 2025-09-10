<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'permissions',
        'is_active',
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    // Permission Methods
    public function hasPermission(string $permission): bool
    {
        if (!is_array($this->permissions)) {
            return false;
        }
        
        return in_array($permission, $this->permissions, true);
    }

    public function givePermission(string $permission): void
    {
        $permissions = $this->permissions ?? [];
        
        if (!in_array($permission, $permissions, true)) {
            $permissions[] = $permission;
            $this->update(['permissions' => $permissions]);
        }
    }

    public function revokePermission(string $permission): void
    {
        $permissions = $this->permissions ?? [];
        
        $this->update([
            'permissions' => array_values(array_filter($permissions, fn($p) => $p !== $permission))
        ]);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBySlug($query, string $slug)
    {
        return $query->where('slug', $slug);
    }

    // Default Permissions Map
    public static function getDefaultPermissions(): array
    {
        return [
            'admin' => [
                'manage_users',
                'manage_roles',
                'upload_images',
                'edit_any_image',
                'delete_any_image',
                'publish_images',
                'moderate_comments',
                'access_admin',
                'manage_albums',
                'manage_collections',
                'view_analytics',
                'manage_settings',
            ],
            'editor' => [
                'upload_images',
                'edit_own_images',
                'delete_own_images',
                'publish_images',
                'manage_own_albums',
                'create_collections',
                'comment_on_images',
                'like_images',
            ],
            'visitor' => [
                'view_public_images',
                'view_public_albums',
                'comment_on_images',
                'like_images',
                'download_public_images',
            ],
        ];
    }
}
