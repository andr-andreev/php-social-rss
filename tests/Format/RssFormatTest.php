<?php
declare(strict_types = 1);


namespace SocialRss\Format;

use SocialRss\Parser\ParserFactory;

/**
 * Class RssFormatTest
 * @package SocialRss\Format
 */
class RssFormatTest extends \PHPUnit_Framework_TestCase
{
    public function testRssFormat()
    {
        $parser = (new ParserFactory())->create('twitter', []);
        $writer = (new FormatFactory())->create('rss');

        $feed = json_decode(file_get_contents(__DIR__ . '/../fixtures/twitter.json'), true);
        $parsedFeed = $parser->parseFeed($feed);

        $out = $writer->format($parsedFeed);

        $lines = explode("\n", $out);

        $this->assertContains('rss version="2.0"', $lines[1]);
    }
}
