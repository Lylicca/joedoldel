<?php

namespace App\Actions;

use App\Models\Comment;
use App\Models\Video;
use App\Services\YoutubeService;
use Illuminate\Support\Facades\DB;

class PurgeHighProbabilitySpam
{
  private YoutubeService $service;

  public function __construct(string $accessToken)
  {
    $this->service = new YoutubeService($accessToken);
  }

  public function execute(string $videoId)
  {
    $incrementedVideoId = Video::where('video_id', $videoId)->firstOrFail(['id']);
    $comments = Comment::where('video_id', $incrementedVideoId->id)
      ->where('spam_probability', '>=', 0.5)
      ->where('removed_at', null)
      ->get();

    DB::transaction(function () use ($comments) {
      foreach ($comments as $comment) {
        // Delete the comment from YouTube
        $this->service->setModerationStatus($comment->comment_id, $comment->spam_probability >= 0.8 ? 'rejected' : 'heldForReview');
      }

      // Mark the comments as removed in the database
      Comment::whereIn('id', $comments->pluck('id'))
        ->update(['removed_at' => now()]);
    });

    return count($comments);
  }
}
