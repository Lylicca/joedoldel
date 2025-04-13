<?php

namespace App\Jobs;

use App\Actions\RefreshGoogleCredentials;
use App\Actions\SyncChannelVideos;
use App\Models\Channel;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SyncVideosFromChannel implements ShouldQueue
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

      foreach ($userChannels as $channel) {
        Log::info("— Syncing videos for channel: {$channel->channel_id}");

        Cache::set(
          "channel.{$channel->id}.videos",
          new SyncChannelVideos($user->google_token)->execute($channel->channel_id),
          now()->addHours(6),
        );

        Log::info("— Synced videos for channel: {$channel->channel_id}");
      }

      Log::info("Synced videos for user: {$user->id}");
    }
  }
}
