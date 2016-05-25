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

namespace Benkle\FeedParser\DOMParsers;


use Benkle\FeedParser\Interfaces\DOMParserInterface;
use Benkle\FeedParser\Standards\Atom\Atom10Standard;

class MastermindsHTML5ParserTest extends \PHPUnit_Framework_TestCase
{

    public function testNewDOMParser()
    {
        $parser = new MastermindsHTML5Parser();
        $this->assertInstanceOf(DOMParserInterface::class, $parser);
    }

    public function testItCanHandleXML()
    {
        $parser = new MastermindsHTML5Parser();
        $xml = file_get_contents(__DIR__ . '/test-data.xml');
        $dom = $parser->parse($xml);

        $titles = $dom->getElementsByTagName('title');
        $this->assertEquals(1, $titles->length);
        $this->assertEquals('PHPUnit Test Feed', $titles->item(0)->nodeValue);

        $contributorNames = $dom->getElementsByTagNameNS(Atom10Standard::NAMESPACE_URI, 'name');
        $this->assertEquals(1, $contributorNames->length);
        $this->assertEquals('Benjamin Kleiner', $contributorNames->item(0)->nodeValue);
    }

    public function testItCannHandleHTML()
    {
        $parser = new MastermindsHTML5Parser();
        $xml = file_get_contents(__DIR__ . '/test-data-with-html.xml');
        $dom = $parser->parse($xml);

        $titles = $dom->getElementsByTagName('title');
        $this->assertEquals(1, $titles->length);
        $this->assertEquals('PHPUnit<br>Test Feed', $titles->item(0)->nodeValue);

        $contributorNames = $dom->getElementsByTagNameNS(Atom10Standard::NAMESPACE_URI, 'name');
        $this->assertEquals(1, $contributorNames->length);
        $this->assertEquals('Benjamin Kleiner', $contributorNames->item(0)->nodeValue);
    }

}
