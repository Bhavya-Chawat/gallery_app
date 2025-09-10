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
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
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

        $users = $query->paginate(15)
            ->through(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_active' => $user->is_active,
                    'email_verified_at' => $user->email_verified_at,
                    'last_login_at' => $user->last_login_at,
                    'storage_used' => $user->storage_used_bytes,
                    'storage_quota' => $user->storage_quota_bytes,
                    'storage_percentage' => $user->getStorageUsagePercentage(),
                    'roles' => $user->roles,
                    'created_at' => $user->created_at,
                ];
            });

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'roles' => Role::active()->get(),
            'filters' => $request->only(['search', 'role', 'status']),
        ]);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return Inertia::render('Admin/Users/Create', [
            'roles' => Role::active()->get(),
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
            'storage_quota_bytes' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'storage_quota_bytes' => $request->storage_quota_bytes,
            'is_active' => $request->is_active ?? true,
            'email_verified_at' => now(), // Admin-created users are auto-verified
        ]);

        $user->assignRole($request->role);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $user->load(['roles', 'images', 'albums', 'comments']);

        $stats = [
            'images_count' => $user->images()->count(),
            'albums_count' => $user->albums()->count(),
            'comments_count' => $user->comments()->count(),
            'total_views' => $user->images()->sum('views_count'),
            'total_likes' => $user->images()->sum('likes_count'),
        ];

        return Inertia::render('Admin/Users/Show', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_active' => $user->is_active,
                'email_verified_at' => $user->email_verified_at,
                'last_login_at' => $user->last_login_at,
                'storage_used' => $user->storage_used_bytes,
                'storage_quota' => $user->storage_quota_bytes,
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
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $this->authorize('update', $user);

        return Inertia::render('Admin/Users/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_active' => $user->is_active,
                'storage_quota_bytes' => $user->storage_quota_bytes,
                'email_notifications' => $user->email_notifications,
                'roles' => $user->roles->pluck('slug'),
            ],
            'roles' => Role::active()->get(),
        ]);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user)],
            'password' => 'nullable|string|min:8|confirmed',
            'storage_quota_bytes' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'email_notifications' => 'boolean',
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'storage_quota_bytes' => $request->storage_quota_bytes,
            'is_active' => $request->is_active ?? true,
            'email_notifications' => $request->email_notifications ?? true,
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        // Prevent deleting the current user
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Assign a role to a user.
     */
    public function assignRole(Request $request, User $user)
    {
        $this->authorize('assignRole', $user);

        $request->validate([
            'role' => 'required|exists:roles,slug',
        ]);

        $user->roles()->detach(); // Remove all existing roles
        $user->assignRole($request->role);

        return redirect()->back()
            ->with('success', 'Role assigned successfully.');
    }

    /**
     * Toggle user active status.
     */
    public function toggleActive(User $user)
    {
        $this->authorize('update', $user);

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'activated' : 'deactivated';

        return redirect()->back()
            ->with('success', "User {$status} successfully.");
    }

    /**
     * Reset user storage usage.
     */
    public function resetStorage(User $user)
    {
        $this->authorize('manageStorage', $user);

        // Recalculate actual storage usage
        $actualUsage = $user->images()->sum('size_bytes');
        $user->update(['storage_used_bytes' => $actualUsage]);

        return redirect()->back()
            ->with('success', 'Storage usage recalculated successfully.');
    }
}
