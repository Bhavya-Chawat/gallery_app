<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckStorageQuota
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->getStorageUsagePercentage() >= 95) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Storage quota exceeded. Please delete some files or contact an administrator.',
                    'storage_usage' => $request->user()->getStorageUsagePercentage()
                ], 413);
            }

            return redirect()->back()->withErrors([
                'storage' => 'Storage quota exceeded. Please delete some files or contact an administrator.'
            ]);
        }

        return $next($request);
    }
}
