<?php
declare(strict_types = 1);


namespace SocialRss\Parser\Vk;

use SocialRss\Parser\Vk\Attachment\AlbumAttachment;
use SocialRss\Parser\Vk\Attachment\AppAttachment;
use SocialRss\Parser\Vk\Attachment\AttachmentInterface;
use SocialRss\Parser\Vk\Attachment\AudioAttachment;
use SocialRss\Parser\Vk\Attachment\DocumentAttachment;
use SocialRss\Parser\Vk\Attachment\GraffitiAttachment;
use SocialRss\Parser\Vk\Attachment\LinkAttachment;
use SocialRss\Parser\Vk\Attachment\NoteAttachment;
use SocialRss\Parser\Vk\Attachment\PageAttachment;
use SocialRss\Parser\Vk\Attachment\PhotoAttachment;
use SocialRss\Parser\Vk\Attachment\PhotosListAttachment;
use SocialRss\Parser\Vk\Attachment\PollAttachment;
use SocialRss\Parser\Vk\Attachment\PostedPhotoAttachment;
use SocialRss\Parser\Vk\Attachment\UnknownAttachment;
use SocialRss\Parser\Vk\Attachment\VideoAttachment;

class AttachmentParser
{
    protected $item;

    protected $attachmentsMap = [
        'photo' => PhotoAttachment::class,
        'posted_photo' => PostedPhotoAttachment::class,
        'video' => VideoAttachment::class,
        'audio' => AudioAttachment::class,
        'doc' => DocumentAttachment::class,
        'graffiti' => GraffitiAttachment::class,
        'link' => LinkAttachment::class,
        'note' => NoteAttachment::class,
        'app' => AppAttachment::class,
        'poll' => PollAttachment::class,
        'page' => PageAttachment::class,
        'album' => AlbumAttachment::class,
        'photos_list' => PhotosListAttachment::class,
    ];

    /**
     * AttachmentParser constructor.
     *
     * @param $item
     */
    public function __construct($item)
    {
        $this->item = $item;
    }

    public function getAttachmentsOutput(): string
    {
        if (!isset($this->item['attachments'])) {
            return '';
        }

        $attachments = array_map(function (array $attachment) {
            $attachmentParser = $this->createParser($attachment);

            return $attachmentParser->getAttachmentOutput();
        }, $this->item['attachments']);

        return implode(PHP_EOL, $attachments);
    }

    protected function createParser(array $attachment): AttachmentInterface
    {
        $map = $this->attachmentsMap;
        $type = $attachment['type'];

        $className = $map[$type] ?? UnknownAttachment::class;

        return new $className($attachment);
    }
}
