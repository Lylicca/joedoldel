<?php

namespace App\Jobs;

use App\Actions\PurgeHighProbabilitySpam;
use App\Models\CleanupStatistics;
use App\Models\CleanupStatisticsDetail;
use App\Models\Video;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CleanSpamComments implements ShouldQueue
{
  use Queueable;

  /**
   * Create a new job instance.
   */
  public function __construct(public string $videoId, public string|null $cleanupStatsId = null)
  {
    //
  }

  /**
   * Execute the job.
   */
  public function handle(): void
  {
    $video = Video::findOrFail($this->videoId);
    $user = $video->channel->user;

    [
      'taken_down' => $takenDownComment,
      'held_for_review' => $heldForReviewComment,
      'total' => $total,
    ] = new PurgeHighProbabilitySpam($user->google_token)->execute($video->video_id);

    if ($this->cleanupStatsId) {
      $stats = CleanupStatistics::findOrFail($this->cleanupStatsId);

      $stats->increment('comments_processed', $total);
      $stats->increment('spam_removed', $takenDownComment);
      $stats->increment('comments_held_for_review', $heldForReviewComment);

      CleanupStatisticsDetail::create([
        'cleanup_statistics_id' => $this->cleanupStatsId,
        'video_id' => $video->id,
        'comments_processed' => $total,
        'spam_removed' => $takenDownComment,
        'comments_held_for_review' => $heldForReviewComment,
      ]);
    }
  }
}
