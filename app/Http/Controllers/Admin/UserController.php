<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->hasRole('admin')) {
                abort(403, 'Admin access required.');
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        try {
            $query = User::with('roles');

            // Search
            if ($request->filled('search')) {
                $query->where(function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('email', 'like', '%' . $request->search . '%');
                });
            }

            // Filter by role
            if ($request->filled('role')) {
                $query->whereHas('roles', function ($q) use ($request) {
                    $q->where('slug', $request->role);
                });
            }

            // Filter by status
            if ($request->filled('status')) {
                $query->where('is_active', $request->status === 'active');
            }

            $users = $query->paginate(15)->through(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_active' => $user->is_active,
                    'email_verified_at' => $user->email_verified_at,
                    'last_login_at' => $user->last_login_at,
                    'storage_used' => $user->storage_used_bytes ?: 0,
                    'storage_quota' => $user->storage_quota_bytes ?: 0,
                    'storage_percentage' => $user->getStorageUsagePercentage(),
                    'roles' => $user->roles,
                    'created_at' => $user->created_at,
                ];
            });

            return Inertia::render('Admin/Users/Index', [
                'users' => $users,
                'roles' => $this->getRoles(),
                'filters' => $request->only(['search', 'role', 'status']),
            ]);
        } catch (\Exception $e) {
            return Inertia::render('Admin/Users/Index', [
                'users' => collect(),
                'roles' => collect(),
                'filters' => $request->only(['search', 'role', 'status']),
                'error' => 'Failed to load users: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return Inertia::render('Admin/Users/Create', [
            'roles' => $this->getRoles(),
        ]);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,slug',
            'storage_quota_gb' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'storage_quota_bytes' => $request->storage_quota_gb * 1024 * 1024 * 1024, // Convert GB to bytes
                'storage_used_bytes' => 0, // Initialize to 0
                'is_active' => $request->is_active ?? true,
                'email_verified_at' => now(), // Admin-created users are auto-verified
                'email_notifications' => true, // Default to true
            ]);

            $user->assignRole($request->role);

            return redirect()->route('admin.users.index')
                ->with('success', 'User created successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create user: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        try {
            $user->load(['roles', 'images', 'albums', 'comments']);

            $stats = [
                'images_count' => $user->images()->count(),
                'albums_count' => $user->albums()->count(),
                'comments_count' => $user->comments()->count(),
                'total_views' => $user->images()->sum('views_count') ?: 0,
                'total_likes' => $user->images()->sum('likes_count') ?: 0,
            ];

            return Inertia::render('Admin/Users/Show', [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_active' => $user->is_active,
                    'email_verified_at' => $user->email_verified_at,
                    'last_login_at' => $user->last_login_at,
                    'storage_used' => $user->storage_used_bytes ?: 0,
                    'storage_quota' => $user->storage_quota_bytes ?: 0,
                    'storage_percentage' => $user->getStorageUsagePercentage(),
                    'roles' => $user->roles,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ],
                'stats' => $stats,
                'recentImages' => $user->images()
                    ->orderBy('created_at', 'desc')
                    ->take(6)
                    ->get(),
                'recentComments' => $user->comments()
                    ->with('image')
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get(),
            ]);
        } catch (\Exception $e) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Failed to load user details: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified user - FIXED: Removed authorization
     */
    public function edit(User $user)
    {
        try {
            return Inertia::render('Admin/Users/Edit', [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_active' => $user->is_active,
                    'storage_quota_gb' => round(($user->storage_quota_bytes ?: 0) / (1024 * 1024 * 1024), 2),
                    'email_notifications' => $user->email_notifications ?? true,
                    'roles' => $user->roles->pluck('slug'),
                ],
                'roles' => $this->getRoles(),
            ]);
        } catch (\Exception $e) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Failed to load user edit form: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified user in storage - FIXED: Removed authorization
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user)],
            'password' => 'nullable|string|min:8|confirmed',
            'storage_quota_gb' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'email_notifications' => 'boolean',
        ]);

        try {
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'storage_quota_bytes' => $request->storage_quota_gb * 1024 * 1024 * 1024,
                'is_active' => $request->is_active ?? true,
                'email_notifications' => $request->email_notifications ?? true,
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $user->update($userData);

            return redirect()->route('admin.users.index')
                ->with('success', 'User updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update user: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified user from storage - FIXED: Removed authorization
     */
    public function destroy(User $user)
    {
        // Prevent deleting the current user
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'You cannot delete your own account.');
        }

        try {
            $user->delete();

            return redirect()->route('admin.users.index')
                ->with('success', 'User deleted successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }

    /**
     * Assign a role to a user - FIXED: Removed authorization
     */
    public function assignRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|exists:roles,slug',
        ]);

        try {
            $user->roles()->detach(); // Remove all existing roles
            $user->assignRole($request->role);

            return redirect()->back()
                ->with('success', 'Role assigned successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to assign role: ' . $e->getMessage());
        }
    }

    /**
     * Toggle user active status - FIXED: Removed authorization
     */
    public function toggleActive(User $user)
    {
        try {
            $user->update(['is_active' => !$user->is_active]);

            $status = $user->is_active ? 'activated' : 'deactivated';

            return redirect()->back()
                ->with('success', "User {$status} successfully.");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to toggle user status: ' . $e->getMessage());
        }
    }

    /**
     * Reset user storage usage - FIXED: Removed authorization
     */
    public function resetStorage(User $user)
    {
        try {
            // Recalculate actual storage usage
            $actualUsage = $user->images()->sum('size_bytes') ?: 0;
            $user->update(['storage_used_bytes' => $actualUsage]);

            return redirect()->back()
                ->with('success', 'Storage usage recalculated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to reset storage: ' . $e->getMessage());
        }
    }

    /**
     * Bulk operations for users - NEW METHOD
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
        ]);

        try {
            $users = User::whereIn('id', $request->user_ids)
                ->where('id', '!=', auth()->id()) // Exclude current user
                ->get();

            $count = 0;

            foreach ($users as $user) {
                switch ($request->action) {
                    case 'activate':
                        $user->update(['is_active' => true]);
                        break;
                    case 'deactivate':
                        $user->update(['is_active' => false]);
                        break;
                    case 'delete':
                        $user->delete();
                        break;
                }
                $count++;
            }

            return redirect()->back()
                ->with('success', "{$count} users {$request->action}d successfully.");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Bulk action failed: ' . $e->getMessage());
        }
    }

    /**
     * Export users data - NEW METHOD
     */
    public function export(Request $request)
    {
        try {
            $users = User::with('roles')->get();

            // TODO: Implement actual CSV/Excel export
            return response()->json([
                'message' => 'User export started',
                'count' => $users->count(),
                'data' => $users->map(function ($user) {
                    return [
                        'name' => $user->name,
                        'email' => $user->email,
                        'status' => $user->is_active ? 'Active' : 'Inactive',
                        'roles' => $user->roles->pluck('name')->implode(', '),
                        'storage_used' => $this->formatBytes($user->storage_used_bytes ?: 0),
                        'created_at' => $user->created_at->format('Y-m-d'),
                    ];
                })
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Export failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get user statistics - NEW METHOD
     */
    public function getStats()
    {
        try {
            return response()->json([
                'total_users' => User::count(),
                'active_users' => User::where('is_active', true)->count(),
                'inactive_users' => User::where('is_active', false)->count(),
                'verified_users' => User::whereNotNull('email_verified_at')->count(),
                'unverified_users' => User::whereNull('email_verified_at')->count(),
                'this_month_users' => User::whereMonth('created_at', now()->month)->count(),
                'roles_distribution' => $this->getRoleDistribution(),
                'storage_stats' => $this->getStorageStats(),
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load stats: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get roles safely - PRIVATE HELPER
     */
    private function getRoles()
    {
        try {
            if (class_exists(Role::class)) {
                return Role::all();
            }
        } catch (\Exception $e) {
            // Log error if needed
        }
        
        return collect([
            ['id' => 1, 'name' => 'Admin', 'slug' => 'admin'],
            ['id' => 2, 'name' => 'User', 'slug' => 'user'],
        ]);
    }

    /**
     * Get role distribution - PRIVATE HELPER
     */
    private function getRoleDistribution()
    {
        try {
            return User::with('roles')
                ->get()
                ->flatMap(function ($user) {
                    return $user->roles->pluck('name');
                })
                ->countBy()
                ->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get storage statistics - PRIVATE HELPER
     */
    private function getStorageStats()
    {
        try {
            return [
                'total_storage_used' => User::sum('storage_used_bytes') ?: 0,
                'total_storage_quota' => User::sum('storage_quota_bytes') ?: 0,
                'average_usage_percentage' => User::avg('storage_used_bytes') ? 
                    (User::avg('storage_used_bytes') / User::avg('storage_quota_bytes')) * 100 : 0,
                'users_over_80_percent' => User::whereRaw('storage_used_bytes > storage_quota_bytes * 0.8')->count(),
            ];
        } catch (\Exception $e) {
            return [
                'total_storage_used' => 0,
                'total_storage_quota' => 0,
                'average_usage_percentage' => 0,
                'users_over_80_percent' => 0,
            ];
        }
    }

    /**
     * Format bytes for display - PRIVATE HELPER
     */
    private function formatBytes($bytes)
    {
        if ($bytes === 0) return '0 B';
        
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $unitIndex = floor(log($bytes, 1024));
        
        return round($bytes / pow(1024, $unitIndex), 2) . ' ' . $units[$unitIndex];
    }
}
