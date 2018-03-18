<?php
declare(strict_types=1);


namespace SocialRss\Parser\Vk;

use SocialRss\Parser\Vk\Post\DummyPost;
use SocialRss\Parser\Vk\Post\PostInterface;
use SocialRss\Parser\Vk\User\UserCollection;

/**
 * Class PostParser
 *
 * @package SocialRss\Parser\Vk
 */
class PostParser
{
    protected $item;
    protected $users;

    protected $typeMap = [
        'post' => Post\PostPost::class,
        'copy' => Post\CopyPost::class,
        'photo' => Post\PhotoPost::class,
        'photo_tag' => Post\PhotoTagPost::class,
        'wall_photo' => Post\WallPhotoPost::class,
        'friend' => Post\FriendPost::class,
        'note' => Post\NotePost::class,
        'audio' => Post\AudioPost::class,
        'video' => Post\VideoPost::class,
    ];

    /**
     * PostParser constructor.
     *
     * @param $item
     * @param $users
     */
    public function __construct(array $item, UserCollection $users)
    {
        $this->item = $item;
        $this->users = $users;
    }

    /**
     * @return PostInterface
     */
    public function createParser(): PostInterface
    {
        $map = $this->typeMap;
        $type = $this->item['type'] ?? $this->item['post_type'];

        $className = $map[$type] ?? DummyPost::class;

        return new $className($this->item, $this->users);
    }
}
