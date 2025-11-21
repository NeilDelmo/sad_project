<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.users.index', compact('users'));
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
        
        return redirect()->back()->with('success', 'User suspended successfully.');
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
}
