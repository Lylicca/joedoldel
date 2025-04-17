<?php

namespace App\Http\Controllers;

use App\Actions\SyncChannelVideos;
use App\Models\Channel;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class ChannelController extends Controller
{
  public function show(string $channel)
  {
    $user = Auth::user();
    $channel = Channel::where('channel_id', $channel)->firstOrFail();

    if ($channel->user_id !== $user->id) {
      return redirect()->back()->with('error', 'You do not have permission to view this channel.');
    }

    return Inertia::render('channels/show', [
      'channel' => $channel,
      'videos' => Inertia::defer(function () use ($channel, $user) {
        Cache::remember(
          "channel.{$channel->id}.videos",
          now()->addHours(6),
          fn() => new SyncChannelVideos($user->google_token)->execute($channel->channel_id)
        );

        return Video::where('channel_id', $channel->id)
          ->orderByRaw("CASE
              WHEN visibility = 'public' THEN 1
              WHEN visibility = 'unlisted' THEN 2
              WHEN visibility = 'private' THEN 3
              ELSE 4
            END")
          ->orderBy('published_at', 'desc')
          ->get();
      }),
    ]);
  }

  public function refresh(string $channel)
  {
    $channel = Channel::findOrFail($channel);

    Cache::forget("channel.{$channel->id}.videos");

    return redirect()->back()->with('success', 'Channel videos refreshed successfully.');
  }
}
