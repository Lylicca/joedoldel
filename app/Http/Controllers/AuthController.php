<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
  /**
   * Show the login view.
   */
  public function login()
  {
    if (Auth::check()) {
      return redirect()->intended('/dashboard');
    }

    return Inertia::location(route('google.login'));
  }

  /**
   * Log the user out of the application.
   */
  public function logout()
  {
    Auth::logout();

    return redirect()->route('home');
  }

  /**
   * Redirect the user to the Google authentication page.
   */
  public function redirect()
  {
    /**
     * @var \Laravel\Socialite\Contracts\Two\GoogleProvider $provider
     */
    $provider = Socialite::driver('google');

    return $provider
      ->scopes([
        'https://www.googleapis.com/auth/youtube.force-ssl',
        'https://www.googleapis.com/auth/youtube',
      ])
      ->with(['access_type' => 'offline', 'prompt' => 'consent select_account'])
      ->redirect();
  }

  /**
   * Obtain the user information from Google.
   */
  public function callback()
  {
    try {
      $googleUser = Socialite::driver('google')->user();
    } catch (Exception $e) {
      // Handle error or redirect back with an error message
      return redirect()->route('login')->with('error', 'Login failed.');
    }

    // Check if the user already exists in your database
    $user = User::where('email', $googleUser->getEmail())->first();

    if (!$user) {
      $user = User::create([
        'name' => $googleUser->getName(),
        'email' => $googleUser->getEmail(),
      ]);
    }

    $user->update([
      'google_token' => $googleUser->token,
      'google_refresh_token' => $googleUser->refreshToken ?? $user->google_refresh_token,
      'google_token_expires_at' => now()->addSeconds($googleUser->expiresIn),
    ]);

    Auth::login($user, true);

    return redirect()->intended('/dashboard');
  }
}
