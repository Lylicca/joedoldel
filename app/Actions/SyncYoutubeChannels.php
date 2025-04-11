<?php

namespace App\Actions;

use App\Models\Channel;
use App\Services\YoutubeService;
use Illuminate\Support\Facades\Auth;

class SyncYoutubeChannels
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
     * Sync YouTube channels with the database.
     *
     * @param array $channels
     * @return void
     */
    public function execute()
    {
        $channels = $this->service->getChannels(['mine' => true], 'snippet,contentDetails,statistics');

        foreach ($channels['items'] as $channel) {
            $channelId = $channel['id'];
            $name = $channel['snippet']['title'];
            $imageUrl = $channel['snippet']['thumbnails']['default']['url'] ?? null;
            $subscriberCount = (int)$channel['statistics']['subscriberCount'];
            $videoCount = (int)$channel['statistics']['videoCount'];

            // Save or update the channel in the database
            Channel::updateOrCreate(
                ['channel_id' => $channelId],
                [
                    'name' => $name,
                    'image_url' => $imageUrl,
                    'subscriber_count' => $subscriberCount,
                    'video_count' => $videoCount,
                    'user_id' => Auth::id(),
                ],
            );
        }

        // Return the list of channels
        return Channel::where('user_id', Auth::id())->get();
    }
}
