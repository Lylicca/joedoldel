<?php

namespace App\Actions;

use App\Models\Video;
use App\Models\Comment;
use App\Services\YoutubeService;
use App\Utils\TextUtils;
use Carbon\Carbon;
use Exception;

class SyncVideoComments
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
   * Sync comments for a specific video
   *
   * @param string $videoId The YouTube video ID
   * @return array The synced comments
   */
  public function execute(string $videoId)
  {
    $video = Video::where('video_id', $videoId)->firstOrFail();

    try {
      $foundComments = [];

      foreach ($this->service->iterateVideoComments($videoId, 'snippet') as $comment) {
        $snippet = $comment['snippet']['topLevelComment']['snippet'];
        $commentId = $comment['id'];
        $foundComments[] = $commentId;

        // Calculate spam probability based on various factors
        $spamProbability = TextUtils::calculateSpamProbability($snippet['textDisplay']);

        Comment::updateOrCreate(
          ['comment_id' => $commentId],
          [
            'video_id' => $video->id,
            'text' => $snippet['textDisplay'],
            'author_name' => $snippet['authorDisplayName'],
            'author_channel_id' => $snippet['authorChannelId']['value'],
            'like_count' => (int)$snippet['likeCount'],
            'published_at' => Carbon::parse($snippet['publishedAt']),
            'spam_probability' => $spamProbability
          ]
        );
      }

      // Mark comments that no longer exist as removed
      Comment::where('video_id', $video->id)
        ->whereNull('removed_at')
        ->whereNotIn('comment_id', $foundComments)
        ->update(['removed_at' => now()]);
    } catch (Exception $e) {
      report($e);
    }

    return Comment::where('video_id', $video->id)->get();
  }
}
