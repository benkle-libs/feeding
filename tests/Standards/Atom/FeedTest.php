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

namespace Benkle\Feeding\Standards\Atom;


use Benkle\Feeding\Interfaces\FeedInterface;
use Benkle\Feeding\Interfaces\ItemInterface;

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
        $this->assertEmpty($feed->getRelations());

        $link = uniqid();
        $feed->setLink($link);
        $this->assertEquals($link, $feed->getLink());
        $this->assertEquals($link, $feed->getRelation('alternate'));

        $link = uniqid();
        $feed->setRelation('alternate', $link);
        $this->assertEquals($link, $feed->getLink());
        $this->assertEquals($link, $feed->getRelation('alternate'));
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
        $this->assertEmpty($feed->getRelations());

        $url = uniqid();
        $feed->setUrl($url);
        $this->assertEquals($url, $feed->getUrl());
        $this->assertEquals($url, $feed->getRelation('self'));

        $url = uniqid();
        $feed->setRelation('self', $url);
        $this->assertEquals($url, $feed->getUrl());
        $this->assertEquals($url, $feed->getRelation('self'));
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
        $relation = uniqid();

        $feed->setTitle($title);
        $feed->setDescription($description);
        $feed->setLink($link);
        $feed->setLastModified($lastModified);
        $feed->setPublicId($publicId);
        $feed->setUrl($url);
        $feed->addItem($feedItem);
        $feed->setRelation('test', $relation);

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

        $this->assertArrayHasKey('relations', $protoJson);
        $this->assertGreaterThanOrEqual(1, count($protoJson['relations']));
        $this->assertContains($relation, $protoJson['relations']);
    }

    public function testWithRelations()
    {
        $feed = $this->create();

        $this->assertEmpty($feed->getRelations());

        $feed->setRelation('test', 'test');
        $this->assertNotEmpty($feed->getRelations());
        $this->assertCount(1, $feed->getRelations());
        $this->assertArrayHasKey('test', $feed->getRelations());
        $this->assertEquals(['test' => 'test'], $feed->getRelations());
        $this->assertEquals('test', $feed->getRelation('test'));

        $feed->setRelation('test', 'test2');
        $this->assertNotEmpty($feed->getRelations());
        $this->assertCount(1, $feed->getRelations());
        $this->assertArrayHasKey('test', $feed->getRelations());
        $this->assertEquals(['test' => 'test2'], $feed->getRelations());
        $this->assertEquals('test2', $feed->getRelation('test'));

        $feed->setRelation('test2', 'test3');
        $this->assertNotEmpty($feed->getRelations());
        $this->assertCount(2, $feed->getRelations());
        $this->assertArrayHasKey('test2', $feed->getRelations());
        $this->assertEquals(['test' => 'test2', 'test2' => 'test3'], $feed->getRelations());
        $this->assertEquals('test3', $feed->getRelation('test2'));
    }

    /**
     * @expectedException \Benkle\Feeding\Exceptions\RelationNotFoundException
     * @expectedExceptionMessage Relation "test" not found
     */
    public function testWithRelationsException()
    {
        $feed = $this->create();
        $this->assertEmpty($feed->getRelations());
        $feed->getRelation('test');
    }

    /**
     * @return Feed
     */
    private function create()
    {
        $feed = new Feed();
        return $feed;
    }

}
