<?php

namespace App\Http\Controllers;

use App\Actions\SyncYoutubeChannels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class DashboardController extends Controller
{
  public function index()
  {
    $user = Auth::user();

    return inertia('dashboard', [
      'channels' => Inertia::defer(
        fn() => Cache::remember(
          "channels.{$user->id}",
          now()->addHour(),
          fn() => new SyncYoutubeChannels($user->google_token)->execute()
        )
      ),
    ]);
  }
}
