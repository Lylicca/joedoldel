<?php

namespace App\Http\Controllers;

use App\Actions\SyncVideoComments;
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
}
