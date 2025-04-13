<?php

namespace App\Actions;

use App\Models\Channel;
use App\Models\Video;
use App\Services\YoutubeService;
use Illuminate\Support\Facades\Auth;

class SyncChannelVideos
{
  private YoutubeService $service;

  /**
   * Create a new class instance.
   */
  public function __construct(string $accessToken)
  {
    $this->service = new YoutubeService($accessToken);
  }

  /**
   * Sync videos for a specific channel
   *
   * @param string $channelId
   * @return void
   */
  public function execute(string $channelId)
  {
    $channel = Channel::where('channel_id', $channelId)->firstOrFail();

    foreach ($this->service->iterateChannelVideos($channelId) as $video) {
      $videoId = $video['snippet']['resourceId']['videoId'];
      $title = $video['snippet']['title'];
      $description = $video['snippet']['description'];
      $thumbnailUrl = $video['snippet']['thumbnails']['maxres']['url']
        ?? $video['snippet']['thumbnails']['standard']['url']
        ?? $video['snippet']['thumbnails']['high']['url']
        ?? $video['snippet']['thumbnails']['medium']['url']
        ?? $video['snippet']['thumbnails']['default']['url']
        ?? null;
      $publishedAt = $video['snippet']['publishedAt'];

      // Get statistics from video details
      $viewsCount = (int)$video['details']['statistics']['viewCount'] ?? 0;
      $commentCount = (int)$video['details']['statistics']['commentCount'] ?? 0;
      $visibility = $video['details']['status']['privacyStatus'] ?? 'public';

      // Determine livestream status
      $livestreamStatus = match ($video['details']['snippet']['liveBroadcastContent'] ?? 'none') {
        'live' => 'live',
        'completed' => 'vod',
        default => 'none'
      };

      Video::updateOrCreate(
        ['video_id' => $videoId],
        [
          'title' => $title,
          'description' => $description,
          'thumbnail_url' => $thumbnailUrl,
          'published_at' => $publishedAt,
          'channel_id' => $channel->id,
          'view_count' => $viewsCount,
          'comment_count' => $commentCount,
          'visibility' => $visibility,
          'livestream_status' => $livestreamStatus,
        ]
      );
    }

    return Video::where('channel_id', $channel->id)->get();
  }
}
