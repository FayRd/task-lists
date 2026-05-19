<?php

namespace App\Http\Controllers\Auth;

use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        $googleUser = Socialite::driver('google')->user();

        // Single query: find by google_id first, fall back to email
        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if ($user) {
            // Existing user - link google_id if not already set
            // Keep existing name
            $user->update([
                'google_id' => $googleUser->getId(),
                'name' => $user->name,
            ]);
        } else {
            // Create user with linked google_id
            $user = User::create(
                [
                    'name'      => $googleUser->getName(),
                    'email'     => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                ]
            );
        }

        Auth::login($user, remember: true);

        return redirect()->intended('/dashboard');
    }
}
