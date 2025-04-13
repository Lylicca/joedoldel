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

    $takenDownComment = 0;
    $heldForReviewComment = 0;

    DB::transaction(function () use ($comments, &$takenDownComment, &$heldForReviewComment) {
      foreach ($comments as $comment) {
        $rejected = $comment->spam_probability >= 0.8;

        if ($rejected) {
          $takenDownComment++;
        } else {
          $heldForReviewComment++;
        }

        // Delete the comment from YouTube
        $this->service->setModerationStatus($comment->comment_id, $rejected ? 'rejected' : 'heldForReview');
      }

      // Mark the comments as removed in the database
      Comment::whereIn('id', $comments->pluck('id'))
        ->update(['removed_at' => now()]);
    });

    return [
      'taken_down' => $takenDownComment,
      'held_for_review' => $heldForReviewComment,
      'total' => $comments->count(),
      'video_id' => $videoId
    ];
  }
}
