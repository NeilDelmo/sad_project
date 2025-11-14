<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureVendorOnboarded
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ($user->user_type === 'vendor')) {
            $prefs = $user->vendorPreference;
            $isOnboardingRoute = $request->routeIs('vendor.onboarding') || $request->routeIs('vendor.onboarding.store');

            if ((!$prefs || !$prefs->onboarding_completed_at) && ! $isOnboardingRoute) {
                return redirect()->route('vendor.onboarding');
            }
        }

        return $next($request);
    }
}
