<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VerificationController extends Controller
{
    public function notice()
    {
        $user = auth()->user();
        
        // Ensure email is verified first
        if (!$user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        if ($user->verification_status === 'approved') {
            return redirect()->route('dashboard');
        }

        return view('auth.verify-documents', compact('user'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'document' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB max
        ]);

        $user = auth()->user();
        
        if ($request->hasFile('document')) {
            // Delete old document if exists
            if ($user->verification_document) {
                Storage::disk('public')->delete($user->verification_document);
            }

            $path = $request->file('document')->store('verification_documents', 'public');
            
            $user->update([
                'verification_document' => $path,
                'verification_status' => 'pending',
            ]);
        }

        return redirect()->route('verification.documents')->with('success', 'Document uploaded successfully. Please wait for admin approval.');
    }
}
