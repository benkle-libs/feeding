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


class EnclosureTest extends \PHPUnit_Framework_TestCase
{

    public function testNewEnclosure()
    {
        $enclosure = $this->create();
        $this->assertInstanceOf(Enclosure::class, $enclosure);
    }

    public function testWithTitle()
    {
        $enclosure = $this->create();
        $title = uniqid();
        $enclosure->setTitle($title);
        $this->assertEquals($title, $enclosure->getTitle());
    }

    public function testWithUrl()
    {
        $enclosure = $this->create();
        $url = uniqid();
        $enclosure->setUrl($url);
        $this->assertEquals($url, $enclosure->getUrl());
    }

    public function testWithLength()
    {
        $enclosure = $this->create();
        $length = rand(400, 5000);
        $enclosure->setLength($length);
        $this->assertEquals($length, $enclosure->getLength());
    }

    public function testWithType()
    {
        $enclosure = $this->create();
        $type = uniqid();
        $enclosure->setType($type);
        $this->assertEquals($type, $enclosure->getType());
    }

    public function testJsonSerialize()
    {
        $enclosure = $this->create();

        $title = uniqid();
        $type = uniqid();
        $url = uniqid();
        $length = rand(400, 5000);

        $enclosure->setTitle($title);
        $enclosure->setUrl($url);
        $enclosure->setType($type);
        $enclosure->setLength($length);

        $protoJson = $enclosure->jsonSerialize();

        $this->assertArrayHasKey('title', $protoJson);
        $this->assertEquals($title, $protoJson['title']);

        $this->assertArrayHasKey('url', $protoJson);
        $this->assertEquals($url, $protoJson['url']);

        $this->assertArrayHasKey('type', $protoJson);
        $this->assertEquals($type, $protoJson['type']);

        $this->assertArrayHasKey('length', $protoJson);
        $this->assertEquals($length, $protoJson['length']);
    }

    /**
     * @return Enclosure
     */
    private function create()
    {
        $feed = new Enclosure();
        return $feed;
    }

}
