<?php

namespace App\Http\Middleware;

use App\Actions\RefreshGoogleCredentials;
use Closure;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\Response;

class RefreshGoogleToken
{
  /**
   * Handle an incoming request.
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
   */
  public function handle(Request $request, Closure $next): Response
  {
    $user = $request->user();

    if (
      $user &&
      $user->google_token_expires_at &&
      $user->google_refresh_token &&
      Carbon::parse($user->google_token_expires_at)->subMinutes(5)->isPast()
    ) {
      try {
        $action = new RefreshGoogleCredentials();
        $action->execute($user);
      } catch (\Exception $e) {
        // If refresh fails, we'll let the request continue and let the application
        // handle any auth errors that might occur
        report($e);
      }
    }

    return $next($request);
  }
}
