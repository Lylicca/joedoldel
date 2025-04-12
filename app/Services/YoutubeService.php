<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Generator;
use Exception;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class YoutubeService
{
  private const API_BASE_URL = 'https://www.googleapis.com/youtube/v3';
  private const QUOTA_LIMIT_PER_DAY = 10000; // Default YouTube API quota
  private const COMMENTS_QUOTA_COST = 1; // Cost per comment list request

  private string $accessToken;

  /**
   * Create a new class instance.
   */
  public function __construct(string $accessToken)
  {
    $this->accessToken = $accessToken;
  }

  /**
   * Get a list of YouTube channels based on various criteria
   *
   * @param array $params Additional parameters (mine, username, ids)
   * @param string $part Comma-separated list of channel resource parts
   * @return array
   */
  public function getChannels(array $params, string $part = 'snippet,contentDetails,statistics'): array
  {
    $queryParams = array_merge([
      'part' => $part,
    ], $params);

    return $this->makeRequest('GET', 'channels', $queryParams);
  }

  /**
   * Get a channel's uploaded videos playlist
   *
   * @param string $channelId
   * @param string $part
   * @return array
   */
  public function getChannelUploads(string $channelId, string $part = 'snippet,contentDetails'): array
  {
    $channel = $this->getChannels(['id' => $channelId], 'contentDetails');
    if (empty($channel['items'])) {
      return [];
    }

    $uploadsPlaylistId = $channel['items'][0]['contentDetails']['relatedPlaylists']['uploads'];
    return $this->getPlaylistItems($uploadsPlaylistId, $part);
  }

  /**
   * Get playlist items
   *
   * @param string $playlistId
   * @param string $part
   * @param string|null $pageToken
   * @return array
   */
  public function getPlaylistItems(string $playlistId, string $part = 'snippet,contentDetails', ?string $pageToken = null): array
  {
    $params = [
      'playlistId' => $playlistId,
      'part' => $part,
      'maxResults' => 50,
    ];

    if ($pageToken) {
      $params['pageToken'] = $pageToken;
    }

    return $this->makeRequest('GET', 'playlistItems', $params);
  }

  /**
   * Iterator for getting all videos of a channel
   *
   * @param string $channelId
   * @param string $part
   * @return Generator
   */
  public function iterateChannelVideos(string $channelId, string $part = 'snippet,contentDetails'): Generator
  {
    $pageToken = null;

    // First get the uploads playlist ID from the channel
    $channelResponse = $this->makeRequest('GET', 'channels', [
      'id' => $channelId,
      'part' => 'contentDetails'
    ]);

    if (empty($channelResponse['items'])) {
      throw new Exception('Channel not found');
    }

    $uploadsPlaylistId = $channelResponse['items'][0]['contentDetails']['relatedPlaylists']['uploads'];

    do {
      $params = [
        'playlistId' => $uploadsPlaylistId,
        'part' => $part,
        'maxResults' => 50
      ];

      if ($pageToken) {
        $params['pageToken'] = $pageToken;
      }

      $response = $this->makeRequest('GET', 'playlistItems', $params);

      if (!isset($response['items'])) {
        throw new Exception('Invalid response from YouTube API: ' . json_encode($response));
      }

      // Get video details
      $videoIds = array_map(function ($item) {
        return $item['contentDetails']['videoId'];
      }, $response['items']);

      $videoDetails = $this->getVideos($videoIds, 'snippet,contentDetails,statistics,status');

      if (!isset($videoDetails['items'])) {
        throw new Exception('Invalid response from YouTube API: ' . json_encode($videoDetails));
      }

      $videoDetails = collect($videoDetails['items'])->keyBy('id')->all();

      foreach ($response['items'] as $video) {
        $video['details'] = $videoDetails[$video['contentDetails']['videoId']] ?? null;

        yield $video;
      }

      $pageToken = $response['nextPageToken'] ?? null;
    } while ($pageToken !== null);
  }

  /**
   * Get video details
   *
   * @param string|array $videoIds Single ID or array of video IDs
   * @param string $part
   * @return array
   */
  public function getVideos(string|array $videoIds, string $part = 'snippet,contentDetails,statistics'): array
  {
    $params = [
      'id' => is_array($videoIds) ? implode(',', $videoIds) : $videoIds,
      'part' => $part,
    ];

    return $this->makeRequest('GET', 'videos', $params);
  }

  /**
   * Iterator for getting all comments of a video with rate limit handling
   *
   * @param string $videoId
   * @param string $part
   * @param int $batchSize
   * @param string|null $startPageToken Start fetching from this page token (for incremental sync)
   * @return Generator
   * @throws Exception
   */
  public function iterateVideoComments(string $videoId, string $part = 'snippet', int $batchSize = 100, ?string $startPageToken = null): Generator
  {
    $pageToken = $startPageToken;
    $quotaKey = 'youtube_quota_' . date('Y-m-d');

    do {
      // Check quota before making the request
      $dailyQuotaUsed = (int) Cache::get($quotaKey, 0);
      if ($dailyQuotaUsed >= self::QUOTA_LIMIT_PER_DAY) {
        $nextReset = Carbon::tomorrow()->startOfDay();
        $waitTime = $nextReset->diffInSeconds(Carbon::now());
        throw new Exception("Daily quota exceeded. Next reset in {$waitTime} seconds.");
      }

      // Make the API request
      $response = $this->getVideoComments($videoId, $part, $pageToken);

      // Update quota usage
      Cache::put($quotaKey, $dailyQuotaUsed + self::COMMENTS_QUOTA_COST, Carbon::tomorrow()->startOfDay());

      // Check for errors in response
      if (!isset($response['items'])) {
        throw new Exception('Invalid response from YouTube API: ' . json_encode($response));
      }

      // Yield each comment
      foreach ($response['items'] as $comment) {
        yield $comment;
      }

      // Get next page token
      $pageToken = $response['nextPageToken'] ?? null;
    } while ($pageToken !== null);
  }

  /**
   * Get video comments with pagination
   *
   * @param string $videoId
   * @param string $part
   * @param string|null $pageToken
   * @return array
   */
  public function getVideoComments(string $videoId, string $part = 'snippet', ?string $pageToken = null): array
  {
    $params = [
      'videoId' => $videoId,
      'part' => $part,
      'maxResults' => 100,
      'textFormat' => 'plainText',
    ];

    if ($pageToken) {
      $params['pageToken'] = $pageToken;
    }

    return $this->makeRequest('GET', 'commentThreads', $params);
  }

  /**
   * Make an HTTP request to the YouTube API
   *
   * @param string $method
   * @param string $endpoint
   * @param array $params
   * @return array
   * @throws Exception
   */
  private function makeRequest(string $method, string $endpoint, array $params = []): array
  {
    try {
      $response = Http::withToken($this->accessToken)
        ->withHeaders([
          'Accept' => 'application/json',
        ])
        ->get(self::API_BASE_URL . "/{$endpoint}", $params);

      if ($response->failed()) {
        throw new Exception("YouTube API error: {$response->body()}");
      }

      return $response->json();
    } catch (Exception $e) {
      // Check if error is quota related
      if (str_contains($e->getMessage(), 'quotaExceeded')) {
        $quotaKey = 'youtube_quota_' . date('Y-m-d');
        Cache::put($quotaKey, self::QUOTA_LIMIT_PER_DAY, Carbon::tomorrow()->startOfDay());
      }
      throw $e;
    }
  }

  /**
   * Delete a YouTube comment
   *
   * @param string $commentId
   * @return bool
   * @throws Exception
   */
  public function deleteComment(string $commentId): bool
  {
    try {
      $response = Http::withToken($this->accessToken)
        ->withHeaders([
          'Accept' => 'application/json',
        ])
        ->delete(self::API_BASE_URL . "/comments", [
          'id' => trim($commentId)
        ]);

      if ($response->failed()) {
        dd($response->body(), $commentId);
        throw new Exception("YouTube API error: {$response->body()}");
      }

      return $response->successful();
    } catch (Exception $e) {
      throw new Exception("Failed to delete comment: " . $e->getMessage());
    }
  }

  /**
   * Set moderation status for a comment
   *
   * @param string $commentId
   * @param string $moderationStatus Can be 'published', 'heldForReview', or 'rejected'
   * @param bool $banAuthor Whether to ban the comment author
   * @return bool
   * @throws Exception
   */
  public function setModerationStatus(string $commentId, string $moderationStatus, bool $banAuthor = false): bool
  {
    try {
      $response = Http::withToken($this->accessToken)
        ->withHeaders([
          'Accept' => 'application/json',
        ])
        ->post(self::API_BASE_URL . "/comments/setModerationStatus", [
          'id' => trim($commentId),
          'moderationStatus' => $moderationStatus,
          'banAuthor' => $banAuthor
        ]);

      if ($response->failed()) {
        throw new Exception("YouTube API error: {$response->body()}");
      }

      return $response->successful();
    } catch (Exception $e) {
      throw new Exception("Failed to set moderation status: " . $e->getMessage());
    }
  }
}
