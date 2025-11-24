<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureBuyer
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user || $user->user_type !== 'buyer') {
            if ($request->expectsJson()) {
                abort(403, 'Only buyers can perform this action.');
            }

            return redirect()->route('marketplace.shop')
                ->withErrors(['error' => 'Only buyers can place orders.']);
        }

        return $next($request);
    }
}
