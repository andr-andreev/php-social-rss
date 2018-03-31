<?php
declare(strict_types=1);


namespace SocialRss\Parser\Instagram;

use SocialRss\Exception\SocialRssException;
use SocialRss\Parser\Feed\BaseFeed;

/**
 * Class InstagramFeed
 * @package SocialRss\Parser\Instagram
 */
class InstagramFeed extends BaseFeed
{

    /**
     * @return array
     * @throws SocialRssException
     */
    public function getItems(): array
    {
        if (isset($this->feed['entry_data']['FeedPage'])) {
            return $this->processFeedPage();
        }
        if (isset($this->feed['entry_data']['ProfilePage'])) {
            return $this->processProfilePage();
        }

        throw new SocialRssException('Feed information is not available.');
    }

    /**
     * @param $items
     * @return array
     */
    protected function processFeed(array $items): array
    {
        return array_map(function ($item) {
            $item['caption'] = $item['caption'] ?? '';
            $item['location']['name'] = $item['location']['name'] ?? '';

            return $item;
        }, $items);
    }

    /**
     * @return array
     * @internal param $feed
     */
    protected function processFeedPage(): array
    {
        $nodes = $this->feed['entry_data']['FeedPage'][0]['graphql']['user']['edge_web_feed_timeline']['edges'];

        // filter nodes without content
        $filteredNodes = array_filter($nodes, function ($node) {
            return in_array($node['node']['__typename'], [
                InstagramFeedItem::TYPE_IMAGE,
                InstagramFeedItem::TYPE_VIDEO,
                InstagramFeedItem::TYPE_CAROUSEL,
            ], true);
        });

        return array_map(function ($node) {
            $nodeData = $node['node'];
            $newData = [
                'date' => $nodeData['taken_at_timestamp'],
                'code' => $nodeData['shortcode'],
                'display_src' => $nodeData['display_url'] ?? '',
                'caption' => $nodeData['edge_media_to_caption']['edges'][0]['node']['text'] ?? '',
            ];

            return array_merge($nodeData, $newData);
        }, $filteredNodes);
    }

    /**
     * @return array
     * @internal param $feed
     */
    protected function processProfilePage(): array
    {
        $items = $this->processFeed($this->feed['entry_data']['ProfilePage'][0]['user']['media']['nodes']);

        $user = $this->feed['entry_data']['ProfilePage'][0]['user'];

        return array_map(function ($item) use ($user) {
            $item['owner'] = $user;

            return $item;
        }, $items);
    }
}
