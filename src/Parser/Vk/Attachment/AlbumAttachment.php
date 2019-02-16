<?php
declare(strict_types=1);


namespace SocialRss\Parser\Vk\Attachment;

class AlbumAttachment extends AbstractAttachment
{
    public function getAttachmentOutput(): string
    {
        $albumTitle = $this->attachment['album']['title'];
        $albumSize = $this->attachment['album']['size'];

        return "Альбом: $albumTitle ($albumSize фото)";
    }
}
