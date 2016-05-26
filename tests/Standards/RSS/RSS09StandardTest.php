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

namespace Benkle\Feeding\Standards\RSS;


use Benkle\Feeding\Interfaces\FeedInterface;
use Benkle\Feeding\Interfaces\StandardInterface;
use Benkle\Feeding\Parser;
use Benkle\Feeding\Utilities\PriorityList;

class RSS09StandardTest extends \PHPUnit_Framework_TestCase
{

    public function testNewStandard()
    {
        $standard = new RSS09Standard();
        $this->assertInstanceOf(StandardInterface::class, $standard);
    }

    public function testNewFeed()
    {
        $standard = new RSS09Standard();
        $this->assertInstanceOf(FeedInterface::class, $standard->newFeed());
    }

    public function testParser()
    {
        $standard = new RSS09Standard();
        $this->assertInstanceOf(Parser::class, $standard->getParser());
    }

    public function testRulset()
    {
        $standard = new RSS09Standard();
        $this->assertInstanceOf(PriorityList::class, $standard->getRules());
    }

    public function testCannotHandleDOMWithoutRootElement()
    {
        $standard = new RSS09Standard();
        $dom = new \DOMDocument();
        $dom->appendChild($dom->createElement('feed'));
        $this->assertEquals(false, $standard->canHandle($dom));
    }

    public function testCannotHandleDOMWithoutProperVersion()
    {
        $standard = new RSS09Standard();
        $dom = new \DOMDocument();
        $dom->appendChild($dom->createElement('rss'));
        $this->assertEquals(false, $standard->canHandle($dom));
    }

    public function testCanHandleDOMWithProperVersion()
    {
        $standard = new RSS09Standard();
        $dom = new \DOMDocument();
        $root = $dom->createElement('rss');
        $version = $dom->createAttribute('version');
        $root->appendChild($version);
        $dom->appendChild($root);

        $versionMap = [
            '0.9'   => true, // Fluke?
            '0.8'   => false,
            '0.80'  => false,
            '0.90'  => true,
            '0.91'  => true,
            '0.92'  => true,
            '0.99'  => true,
            '1.0'   => false,
            '2.0'   => false,
            '2.0.1' => false,
            '2.01'  => false,
            '2.1'   => false,
        ];

        foreach ($versionMap as $versionNr => $canHandle) {
            $version->nodeValue = $versionNr;
            $this->assertEquals($canHandle, $standard->canHandle($dom));
        }
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Invalid number of <rss> tags: 0
     */
    public function testFailWhenNoRootIsFound()
    {
        $standard = new RSS09Standard();
        $dom = new \DOMDocument();
        $dom->appendChild($dom->createElement('feed'));
        $standard->getRootNode($dom);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Invalid number of <rss> tags: 2
     */
    public function testFailWhenToManyRootsAreFound()
    {
        $standard = new RSS09Standard();
        $dom = new \DOMDocument();
        $dom->appendChild($dom->createElement('rss'));
        $dom->appendChild($dom->createElement('rss'));
        $standard->getRootNode($dom);
    }

    public function testSucceedWhenThereSOnlyOneRoot()
    {
        $standard = new RSS09Standard();
        $dom = new \DOMDocument();
        $root = $dom->createElement('rss');
        $dom->appendChild($root);
        $this->assertEquals($root, $standard->getRootNode($dom));
    }

}
