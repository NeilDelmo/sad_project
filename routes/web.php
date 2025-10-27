<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Models\User;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::get('/test-profile', function () {
    $user = User::first();

    if (! $user) {
        return response()->json(['error' => 'No users found'], 404);
    }

    // Create a profile if none exists; otherwise return the existing one
    $profile = $user->fishermanProfile;
    if (! $profile) {
        $profile = $user->fishermanProfile()->create([
            'vessel_name' => 'Blue Dolphin',
            'vessel_type' => 'bangka',
        ]);
    }

    return response()->json($profile);
});
