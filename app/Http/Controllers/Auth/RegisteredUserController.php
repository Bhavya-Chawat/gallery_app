<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified_at' => now(), // Auto-verify to skip email verification
        ]);

        // Auto-assign role based on user count
        $this->assignRoleToUser($user);

        event(new Registered($user));
        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }

    /**
     * Assign role to user automatically
     */
    private function assignRoleToUser(User $user): void
    {
        // Check if this is the first user - make them admin
        if (User::count() === 1) {
            $adminRole = Role::where('slug', 'admin')->first();
            if ($adminRole) {
                $user->roles()->attach($adminRole->id);
                \Log::info("First user {$user->email} assigned admin role");
                return;
            }
        }

        // Default role for all other users is visitor
        $visitorRole = Role::where('slug', 'visitor')->first();
        
        if ($visitorRole) {
            $user->roles()->attach($visitorRole->id);
            \Log::info("User {$user->email} assigned visitor role");
        } else {
            \Log::error('Visitor role not found during user registration', ['user_id' => $user->id]);
        }
    }
}
