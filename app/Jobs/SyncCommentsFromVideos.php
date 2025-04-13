<?php

namespace App\Jobs;

use App\Actions\RefreshGoogleCredentials;
use App\Actions\SyncVideoComments;
use App\Models\Channel;
use App\Models\CleanupStatistics;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SyncCommentsFromVideos implements ShouldQueue
{
  use Queueable;

  /**
   * The action to refresh Google credentials.
   *
   * @var RefreshGoogleCredentials
   */
  protected RefreshGoogleCredentials $refreshAction;

  /**
   * Create a new job instance.
   */
  public function __construct()
  {
    $this->refreshAction = new RefreshGoogleCredentials();
  }

  /**
   * Execute the job.
   */
  public function handle(): void
  {
    $users = User::where('google_refresh_token', '!=', null)->get();

    // Update all users tokens
    foreach ($users as $user) {
      Log::info("Syncing videos for user: {$user->id}");

      $this->refreshAction->execute($user);
      $user = $user->refresh();

      $userChannels = Channel::where('user_id', $user->id)->get();
      $stats = CleanupStatistics::create([
        'cleanup_date' => now(),
        'videos_processed' => 0,
        'comments_processed' => 0,
        'spam_removed' => 0,
        'comments_held_for_review' => 0,
      ]);

      foreach ($userChannels as $channel) {
        Log::info("— Syncing videos for channel: {$channel->channel_id}");

        $videos = $channel->videos()
          ->where('visibility', 'public')->get();

        $stats->increment('videos_processed', $videos->count());

        foreach ($videos as $video) {
          Log::info("—— Syncing comments for video: {$video->video_id}");

          Cache::set(
            "video_comments_{$video->id}",
            new SyncVideoComments($user->google_token)->execute($video->video_id),
            now()->addMinutes(30),
          );

          $stats->increment('comments_processed', $video->comments->count());

          CleanSpamComments::dispatch($video->id, $stats->id);

          Log::info("—— Synced comments for video: {$video->video_id}");
        }

        Log::info("— Synced comments for all videos in channel: {$channel->channel_id}");
      }

      Log::info("Synced comments for all channels of user: {$user->id}");
    }
  }
}
