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

namespace Benkle\Feeding;


use Benkle\Feeding\Interfaces\ItemInterface;

class FeedItemTest extends \PHPUnit_Framework_TestCase
{

    public function testNewFeedItem()
    {
        $feedItem = $this->create();
        $this->assertInstanceOf(ItemInterface::class, $feedItem);
    }

    public function testWithPublicId()
    {
        $feedItem = $this->create();
        $publicId = uniqid();
        $feedItem->setPublicId($publicId);
        $this->assertEquals($publicId, $feedItem->getPublicId());
    }

    public function testWithLastModified()
    {
        $feedItem = $this->create();
        $lastModified = new \DateTime('2000-01-01');
        $this->assertInstanceOf(\DateTime::class, $feedItem->getLastModified());
        $this->assertNotEquals($lastModified, $feedItem->getLastModified());
        $feedItem->setLastModified($lastModified);
        $this->assertEquals($lastModified, $feedItem->getLastModified());
    }

    public function testWithLink()
    {
        $feedItem = $this->create();
        $link = uniqid();
        $feedItem->setLink($link);
        $this->assertEquals($link, $feedItem->getLink());
    }

    public function testWithDescription()
    {
        $feedItem = $this->create();
        $description = uniqid();
        $feedItem->setDescription($description);
        $this->assertEquals($description, $feedItem->getDescription());
    }

    public function testWithTitle()
    {
        $feedItem = $this->create();
        $title = uniqid();
        $feedItem->setTitle($title);
        $this->assertEquals($title, $feedItem->getTitle());
    }

    public function testJsonSerialize()
    {
        $feedItem = $this->create();

        $title = uniqid();
        $description = uniqid();
        $link = uniqid();
        $lastModified = new \DateTime('2000-01-01');
        $publicId = uniqid();
        $relation = uniqid();

        $feedItem->setTitle($title);
        $feedItem->setDescription($description);
        $feedItem->setLink($link);
        $feedItem->setLastModified($lastModified);
        $feedItem->setPublicId($publicId);
        $feedItem->setRelation('test', $relation);

        $protoJson = $feedItem->jsonSerialize();

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

        $this->assertArrayHasKey('relations', $protoJson);
        $this->assertCount(1, $protoJson['relations']);
        $this->assertContains($relation, $protoJson['relations']);
    }

    /**
     * @return FeedItem
     */
    private function create()
    {
        $feedItem = new FeedItem();
        return $feedItem;
    }

}
