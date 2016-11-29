<?php
declare(strict_types = 1);


namespace SocialRss\Parser\Vk\Posts;

/**
 * Class NotePost
 * @package SocialRss\Parser\Vk\Posts
 */
class NotePost extends AbstractPost implements PostInterface
{
    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->getUserName() . ': новая заметка';
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return self::URL . "{$this->users[$this->item['source_id']]['screen_name']}";
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {

        $notes = $this->item['notes'];

        $notes = array_map(function ($note) {
            return 'Заметка: ' . $this->makeLink($note['view_url'], $note['title']);
        }, $notes);

        return implode(PHP_EOL, $notes);
    }
}
