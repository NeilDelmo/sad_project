<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Role-based redirection
        $user = Auth::user();
        
        return redirect()->intended($this->redirectPath($user));
    }

    /**
     * Determine where to redirect user based on their role
     */
    protected function redirectPath($user): string
    {
        return match($user->user_type) {
            'admin' => route('dashboard', absolute: false),           // Admin needs dashboard for management
            'regulator' => route('dashboard', absolute: false),       // Regulator needs dashboard for oversight
            'vendor' => route('vendor.dashboard', absolute: false),    // Vendor-specific dashboard (inventory + browse)
            'fisherman' => route('fisherman.dashboard', absolute: false), // Fisherman dashboard (products + safety nav/ML)
            'buyer' => route('marketplace.shop', absolute: false),    // Buyer goes to marketplace to buy
            default => route('marketplace.shop', absolute: false),    // Default to marketplace
        };
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
