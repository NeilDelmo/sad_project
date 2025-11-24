<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsVerified
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

        // Buyers don't need verification (unless you want them to)
        if ($user->user_type === 'buyer' || $user->user_type === 'admin' || $user->user_type === 'regulator') {
            return $next($request);
        }

        // 1. Check Email Verification First
        if (!$user->hasVerifiedEmail()) {
            // If accessing verification routes, allow it
            if ($request->routeIs('verification.notice') || 
                $request->routeIs('verification.verify') || 
                $request->routeIs('verification.send') || 
                $request->routeIs('logout')) {
                return $next($request);
            }
            return redirect()->route('verification.notice');
        }

        // 2. Check Document Verification
        if ($user->verification_status === 'approved') {
            return $next($request);
        }

        // If not approved, redirect to document upload page
        // But allow access to the verification routes themselves to avoid infinite loop
        if ($request->routeIs('verification.documents') || 
            $request->routeIs('verification.upload') || 
            $request->routeIs('logout')) {
            return $next($request);
        }

        return redirect()->route('verification.documents');
    }
}
