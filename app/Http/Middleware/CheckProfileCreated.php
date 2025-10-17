<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckProfileCreated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
         $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // ✅ Allow if user is superuser
        if ($user->is_superuser ?? false) {
            return $next($request);
        }
        // ✅ Allow if user type is admin or superadmin
        if (
            $user->userType &&
            in_array(strtolower($user->userType->name), ['superadmin', 'admin'])
        ) {
            return $next($request);
        }
        // 🚫 Redirect if profile not created
        if (!$user->profile_created) {
            if (
                !$request->is('profile/create') &&
                !$request->is('profile/store')
            ) {
                return redirect()
                    ->route('profile.create')
                    ->with('alert', 'Please complete your profile before accessing the dashboard.');
            }
        }
        return $next($request);
    }
}
