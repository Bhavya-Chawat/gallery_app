<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        if ($user) {
            $user->loadMissing('roles');
        }
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->roles?->pluck('slug') ?? [],
                    'permissions' => $user->roles?->pluck('permissions')->flatten()->unique()->values() ?? [],
                ] : null,
            ],
            'app' => [
                'name' => config('app.name', 'Laravel Gallery'),
            ],
            'can' => [
                'login' => \Illuminate\Support\Facades\Route::has('login'),
                'register' => \Illuminate\Support\Facades\Route::has('register') && (bool) config('gallery.enable_registration', true),
            ],
        ];
    }
}
