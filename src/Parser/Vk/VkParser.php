<?php
declare(strict_types = 1);

namespace SocialRss\Parser\Vk;

use SocialRss\Parser\AbstractParser;
use SocialRss\Parser\Feed\FeedInterface;
use SocialRss\Parser\FeedItem\FeedItemInterface;

/**
 * Class VkParser
 *
 * @package SocialRss\Parser\Vk
 */
class VkParser extends AbstractParser
{
    protected $vkClient;

    public function __construct(array $config)
    {
        $this->vkClient = new VkClient($config);
    }

    /**
     * @throws \SocialRss\Exception\SocialRssException
     * @throws \VK\Exceptions\VKClientException
     */
    public function getFeed(string $username): array
    {
        return $this->vkClient->getFeed($username);
    }

    public static function getName(): string
    {
        return 'VK';
    }

    public static function getUrl(): string
    {
        return 'https://vk.com/';
    }

    public function getFeedParser(array $feed): FeedInterface
    {
        return new VkFeed($feed);
    }

    /**
     * @param array $item
     * @return FeedItemInterface
     */
    public function createFeedItemParser(array $item): FeedItemInterface
    {
        return new VkFeedItem($item);
    }
}
