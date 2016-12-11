<?php
declare(strict_types = 1);


namespace SocialRss\Parser\Vk\Posts;

/**
 * Class AudioPost
 *
 * @package SocialRss\Parser\Vk\Posts
 */
class AudioPost extends AbstractPost implements PostInterface
{
    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->getUserName() . ': новые аудиозаписи';
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return self::URL . "audios{$this->users[$this->item['source_id']]['id']}";
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        $audios = $this->item['audio'];

        $audios = array_filter(
            $audios, function ($audio) {
            return isset($audio['title']);
        }
        );

        $audios = array_map(
            function ($audio) {
                return "Аудиозапись: {$audio['artist']} &ndash; {$audio['title']}";
            }, $audios
        );

        return implode(PHP_EOL, $audios);
    }
}
