<?php
declare(strict_types=1);


namespace SocialRss\Parser\Twitter;

use SocialRss\ParsedFeed\ParsedFeedItem;
use SocialRss\Parser\FeedItem\FeedItemInterface;
use SocialRss\Parser\Twitter\Entity\EntityInterface;
use SocialRss\Parser\Twitter\Entity\HashtagEntity;
use SocialRss\Parser\Twitter\Entity\MediaPhotoEntity;
use SocialRss\Parser\Twitter\Entity\MediaVideoEntity;
use SocialRss\Parser\Twitter\Entity\SymbolEntity;
use SocialRss\Parser\Twitter\Entity\UnknownEntity;
use SocialRss\Parser\Twitter\Entity\UrlEntity;
use SocialRss\Parser\Twitter\Entity\UserMentionEntity;

/**
 * Class TwitterFeedItem
 * @package SocialRss\Parser\Twitter
 */
class TwitterFeedItem implements FeedItemInterface
{
    protected $tweet;
    protected $originalTweet;

    /**
     * TwitterFeedItem constructor.
     * @param array $item
     */
    public function __construct(array $item)
    {
        $this->tweet = $item['retweeted_status'] ?? $item;
        $this->originalTweet = $item;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        $title = $this->getAuthorName();
        if ($this->isRetweetedStatus()) {
            $title .= " (RT by {$this->getOriginalAuthorName()})";
        }

        return $title;
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return TwitterParser::getUrl() . "{$this->tweet['user']['screen_name']}/status/{$this->tweet['id_str']}";
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        $flatEntities = $this->getEntities();
        $entitiesMap = $this->getEntitiesMap();

        $processedText = $this->tweet['full_text'];
        foreach ($flatEntities as $entity) {
            foreach ($entitiesMap as $entityItem) {
                if ($entityItem::isApplicable($entity)) {
                    /** @var EntityInterface $entityParser */
                    $entityParser = new $entityItem($entity, $processedText);

                    $processedText = $entityParser->getParsedContent();
                }
            }
        }

        return nl2br(trim($processedText));
    }

    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        return \DateTime::createFromFormat('D M j H:i:s P Y', $this->originalTweet['created_at']);
    }

    /**
     * @return array
     */
    public function getTags(): array
    {
        if (!isset($this->tweet['entities']['hashtags'])) {
            return [];
        }

        return array_map(function ($hashtag) {
            return $hashtag['text'];
        }, $this->tweet['entities']['hashtags']);
    }

    /**
     * @return string
     */
    public function getAuthorName(): string
    {
        return $this->tweet['user']['name'];
    }

    /**
     * @return string
     */
    protected function getOriginalAuthorName(): string
    {
        return $this->originalTweet['user']['name'];
    }

    /**
     * @return mixed
     */
    public function getAuthorAvatar()
    {
        return $this->tweet['user']['profile_image_url_https'];
    }

    /**
     * @return string
     */
    public function getAuthorLink(): string
    {
        return TwitterParser::getUrl() . $this->tweet['user']['screen_name'];
    }

    /**
     * @return null|ParsedFeedItem
     */
    public function getQuote():?ParsedFeedItem
    {
        if (!$this->hasQuote()) {
            return null;
        }

        $feedItem = new self($this->tweet['quoted_status']);

        return new ParsedFeedItem(
            $feedItem->getTitle(),
            $feedItem->getLink(),
            $feedItem->getContent()
        );
    }

    protected function getEntities()
    {
        $tweetEntities = array_merge(
            $this->tweet['entities'],
            $this->tweet['extended_entities'] ?? []
        );

        $processedEntities = array_map(function ($type, $typeArray) {
            return array_map(function ($entity) use ($type) {
                $entity['entity_type'] = isset($entity['type']) ? "{$type}_{$entity['type']}" : $type;

                return $entity;
            }, $typeArray);
        }, array_keys($tweetEntities), $tweetEntities);

        return array_merge(...$processedEntities);
    }

    protected function getEntitiesMap(): array
    {
        return [
            HashtagEntity::class,
            UserMentionEntity::class,
            UrlEntity::class,
            SymbolEntity::class,
            MediaPhotoEntity::class,
            MediaVideoEntity::class,
            UnknownEntity::class,
        ];
    }

    /**
     * @return bool
     */
    protected function hasQuote(): bool
    {
        return isset($this->tweet['quoted_status']);
    }

    /**
     * @return bool
     */
    protected function isRetweetedStatus(): bool
    {
        return isset($this->originalTweet['retweeted_status']);
    }
}
