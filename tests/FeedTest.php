<?php
/**
 * Copyright (c) 2016 Benjamin Kleiner
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of
 * the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS
 * OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
 * OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace Benkle\FeedParser;


use Benkle\FeedParser\Interfaces\FeedInterface;
use Benkle\FeedParser\Interfaces\ItemInterface;

class FeedTest extends \PHPUnit_Framework_TestCase
{

    public function testNewFeed()
    {
        $feed = $this->create();
        $this->assertInstanceOf(FeedInterface::class, $feed);
    }

    public function testWithLink()
    {
        $feed = $this->create();
        $link = uniqid();
        $feed->setLink($link);
        $this->assertEquals($link, $feed->getLink());
    }

    public function testWithTitle()
    {
        $feed = $this->create();
        $title = uniqid();
        $feed->setTitle($title);
        $this->assertEquals($title, $feed->getTitle());
    }

    public function testWithPublicId()
    {
        $feed = $this->create();
        $publicId = uniqid();
        $feed->setPublicId($publicId);
        $this->assertEquals($publicId, $feed->getPublicId());
    }

    public function testWithDescription()
    {
        $feed = $this->create();
        $description = uniqid();
        $feed->setDescription($description);
        $this->assertEquals($description, $feed->getDescription());
    }

    public function testWithLastModified()
    {
        $feed = $this->create();
        $lastModified = new \DateTime('2000-01-01');
        $this->assertInstanceOf(\DateTime::class, $feed->getLastModified());
        $this->assertNotEquals($lastModified, $feed->getLastModified());
        $feed->setLastModified($lastModified);
        $this->assertEquals($lastModified, $feed->getLastModified());
    }

    public function testWithUrl()
    {
        $feed = $this->create();
        $url = uniqid();
        $feed->setUrl($url);
        $this->assertEquals($url, $feed->getUrl());
    }

    public function testWithFeedItems()
    {
        $feed = $this->create();
        $this->assertEmpty($feed->getItems());
        $feedItem = $this->getMock(ItemInterface::class);
        $feed->addItem($feedItem);
        $this->assertCount(1, $feed->getItems());
        $this->assertContains($feedItem, $feed->getItems());
        $feed->removeItem($feedItem);
        $this->assertCount(0, $feed->getItems());
        $this->assertNotContains($feedItem, $feed->getItems());
    }

    public function testJsonSerialize()
    {
        $feed = $this->create();

        $title = uniqid();
        $description = uniqid();
        $link = uniqid();
        $lastModified = new \DateTime('2000-01-01');
        $publicId = uniqid();
        $url = uniqid();
        $feedItem = $this->getMock(ItemInterface::class);

        $feed->setTitle($title);
        $feed->setDescription($description);
        $feed->setLink($link);
        $feed->setLastModified($lastModified);
        $feed->setPublicId($publicId);
        $feed->setUrl($url);
        $feed->addItem($feedItem);

        $protoJson = $feed->jsonSerialize();

        $this->assertArrayHasKey('title', $protoJson);
        $this->assertEquals($title, $protoJson['title']);

        $this->assertArrayHasKey('description', $protoJson);
        $this->assertEquals($description, $protoJson['description']);

        $this->assertArrayHasKey('lastModified', $protoJson);
        $this->assertEquals($lastModified->format(\DateTime::ATOM), $protoJson['lastModified']);

        $this->assertArrayHasKey('publicId', $protoJson);
        $this->assertEquals($publicId, $protoJson['publicId']);

        $this->assertArrayHasKey('link', $protoJson);
        $this->assertEquals($link, $protoJson['link']);

        $this->assertArrayHasKey('url', $protoJson);
        $this->assertEquals($url, $protoJson['url']);

        $this->assertArrayHasKey('items', $protoJson);
        $this->assertCount(1, $protoJson['items']);
        $this->assertContains($feedItem, $protoJson['items']);
    }

    /**
     * @return Feed
     */
    private function create()
    {
        $feedItem = new Feed();
        return $feedItem;
    }

}
