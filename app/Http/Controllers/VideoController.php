<?php

namespace App\Http\Controllers;

use App\Actions\SyncVideoComments;
use App\Actions\PurgeHighProbabilitySpam;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class VideoController extends Controller
{
  public function show(string $id)
  {
    $user = Auth::user();
    $video = Video::where('video_id', $id)
      ->with('channel')
      ->firstOrFail();

    if ($video->channel->user_id !== $user->id) {
      return redirect()
        ->back()
        ->with('error', 'You do not have permission to view this video.');
    }

    return Inertia::render('video/show', [
      'video' => $video,
      'channel' => $video->channel,
      'comments' => Inertia::defer(function () use ($video, $user) {
        Cache::remember(
          "video_comments_{$video->id}",
          now()->addMinutes(30),
          fn() => new SyncVideoComments($user->google_token)->execute($video->video_id)
        );

        return $video->comments()
          ->where('removed_at', null)
          ->orderBy('spam_probability', 'desc')
          ->orderBy('published_at', 'desc')
          ->get();
      }),
    ]);
  }

  public function refresh(string $id)
  {
    $video = Video::find($id);
    if (!$video) {
      return redirect()
        ->back()
        ->with('error', 'Video not found.');
    }

    Cache::forget("video_comments_{$video->id}");

    return redirect()
      ->back()
      ->with('success', 'Comments refreshed successfully.');
  }

  public function purgeSpam(string $id)
  {
    $user = Auth::user();
    $video = Video::find($id);

    if (!$video) {
      return redirect()
        ->back()
        ->with('error', 'Video not found.');
    }

    if ($video->channel->user_id !== $user->id) {
      return redirect()
        ->back()
        ->with('error', 'You do not have permission to purge spam from this video.');
    }

    [
      'taken_down' => $taken_down,
      'held_for_review' => $held_for_review,
    ] = (new PurgeHighProbabilitySpam($user->google_token))->execute($video->video_id);

    Cache::forget("video_comments_{$video->id}");

    $purged = $taken_down + $held_for_review;

    return redirect()
      ->back()
      ->with('success', "Successfully removed {$purged} spam comments.");
  }
}
