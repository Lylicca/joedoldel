<?php

namespace App\Actions;

use App\Models\User;
use Laravel\Socialite\Facades\Socialite;

class RefreshGoogleCredentials
{
  /**
   * Create a new class instance.
   */
  public function __construct()
  {
    //
  }

  public function execute(User $user)
  {
    /**
     * @var \Laravel\Socialite\Contracts\Two\GoogleProvider $provider
     */
    $provider = Socialite::driver('google');
    $creds = $provider
      ->stateless()
      ->scopes([
        'https://www.googleapis.com/auth/youtube.force-ssl',
        'https://www.googleapis.com/auth/youtube',
      ])
      ->with(['access_type' => 'offline'])
      ->refreshToken($user->google_refresh_token);

    if ($creds->refreshToken) {
      $user->update([
        'google_token' => $creds->token,
        'google_refresh_token' => $creds->refreshToken,
        'google_token_expires_at' => now()->addSeconds($creds->expiresIn),
      ]);

      return true;
    }

    return false;
  }
}
