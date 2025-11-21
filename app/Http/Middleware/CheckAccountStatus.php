<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAccountStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            
            if ($user->account_status === 'suspended') {
                auth()->logout();
                return redirect('/login')->with('error', 'Your account has been suspended. Please contact support.');
            }
            
            if ($user->account_status === 'banned') {
                auth()->logout();
                return redirect('/login')->with('error', 'Your account has been banned. Please contact support.');
            }
        }
        
        return $next($request);
    }
}
