<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // Search filter
        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Role filter
        if ($role = $request->get('role')) {
            $query->where('user_type', $role);
        }

        // Status filter
        if ($status = $request->get('status')) {
            $query->where('account_status', $status);
        }

        // Sorting
        $sort = $request->get('sort', 'newest');
        
        switch ($sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'trust_high':
                $query->orderBy('trust_score', 'desc');
                break;
            case 'trust_low':
                $query->orderBy('trust_score', 'asc');
                break;
            case 'alpha_asc':
                $query->orderBy('username', 'asc');
                break;
            case 'alpha_desc':
                $query->orderBy('username', 'desc');
                break;
            case 'most_penalized':
                $query->withCount('infractions')->orderBy('infractions_count', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $users = $query->paginate(20)->withQueryString();
        
        // Simple analytics
        $totalUsers = User::count();
        $activeUsers = User::where('account_status', 'active')->count();
        $suspendedUsers = User::where('account_status', 'suspended')->count();
        $bannedUsers = User::where('account_status', 'banned')->count();
        $newUsersToday = User::whereDate('created_at', today())->count();

        return view('admin.users.index', compact('users', 'totalUsers', 'activeUsers', 'suspendedUsers', 'bannedUsers', 'newUsersToday'));
    }

    public function suspend($id)
    {
        $user = User::findOrFail($id);
        
        // Don't allow suspending admins or current user
        if ($user->user_type === 'admin' || $user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Cannot suspend this user.');
        }
        
        $user->account_status = 'suspended';
        $user->save();

        // Send email notification
        try {
            $user->notify(new \App\Notifications\AccountSuspended());
        } catch (\Exception $e) {
            // Log error but continue
            \Illuminate\Support\Facades\Log::error('Failed to send suspension email: ' . $e->getMessage());
        }
        
        return redirect()->back()->with('success', 'User suspended successfully and notified via email.');
    }

    public function ban($id)
    {
        $user = User::findOrFail($id);
        
        // Don't allow banning admins or current user
        if ($user->user_type === 'admin' || $user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Cannot ban this user.');
        }
        
        $user->account_status = 'banned';
        $user->save();
        
        return redirect()->back()->with('success', 'User banned successfully.');
    }

    public function reactivate($id)
    {
        $user = User::findOrFail($id);
        
        $user->account_status = 'active';
        $user->save();
        
        return redirect()->back()->with('success', 'User reactivated successfully.');
    }

    public function penalty(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        // Don't allow penalizing admins or current user
        if ($user->user_type === 'admin' || $user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Cannot penalize this user.');
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:255',
            'severity' => 'required|in:low,medium,high,critical',
            'details' => 'nullable|string',
        ]);

        $user->applyPenalty(
            $validated['reason'],
            $validated['severity'],
            auth()->user(),
            $validated['details']
        );

        // Refresh user to get updated score
        $user->refresh();

        return redirect()->back()->with('success', "Penalty applied successfully. New Trust Score: {$user->trust_score} ({$user->trust_tier}).");
    }

    public function approveVerification($id)
    {
        $user = User::findOrFail($id);
        $user->update(['verification_status' => 'approved']);
        
        // Optional: Send email notification
        
        return redirect()->back()->with('success', 'User verification approved.');
    }

    public function rejectVerification($id)
    {
        $user = User::findOrFail($id);
        $user->update(['verification_status' => 'rejected']);
        
        // Optional: Send email notification with reason
        
        return redirect()->back()->with('success', 'User verification rejected.');
    }
}
