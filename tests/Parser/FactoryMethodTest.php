<?php
declare(strict_types = 1);


namespace SocialRss\Parser;

use PHPUnit\Framework\TestCase;
use SocialRss\Parser\Instagram\InstagramParser;
use SocialRss\Parser\Twitter\TwitterParser;
use SocialRss\Parser\Vk\VkParser;

/**
 * Class FactoryMethodTest
 *
 * @package SocialRss\Parser
 */
class FactoryMethodTest extends TestCase
{
    public function testCanCreateInstagramParser()
    {
        $factory = new ParserFactory();
        $result = $factory->create('instagram', []);

        $this->assertInstanceOf(InstagramParser::class, $result);
    }

    public function testCanCreateTwitterParser()
    {
        $factory = new ParserFactory();
        $result = $factory->create('twitter', []);

        $this->assertInstanceOf(TwitterParser::class, $result);
    }

    public function testCanCreateVkParser()
    {
        $factory = new ParserFactory();
        $result = $factory->create('vk', ['access_token' => '']);

        $this->assertInstanceOf(VkParser::class, $result);
    }

    public function testUnknownType()
    {
        $this->expectException(\InvalidArgumentException::class);

        (new ParserFactory())->create('youtube', []);
    }
}
