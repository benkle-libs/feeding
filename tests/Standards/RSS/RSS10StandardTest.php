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


use Benkle\Feeding\Interfaces\StandardInterface;

class RSS10StandardTest extends \PHPUnit_Framework_TestCase
{

    public function testNewStandard()
    {
        $standard = new RSS10Standard();
        $this->assertInstanceOf(StandardInterface::class, $standard);
    }

    public function testCannotHandleDOMWithoutRootElement()
    {
        $standard = new RSS10Standard();
        $dom = new \DOMDocument();
        $dom->appendChild($dom->createElement('channel'));
        $this->assertEquals(false, $standard->canHandle($dom));
    }

    public function testCannotHandleDOMWithoutChannelElement()
    {
        $standard = new RSS10Standard();
        $dom = new \DOMDocument();
        $dom->appendChild($dom->createElementNS(RSS10Standard::NAMESPACE_URI, 'RDF'));
        $this->assertEquals(false, $standard->canHandle($dom));
    }

    public function testCannHandleDOMWithChannelandRootElement()
    {
        $standard = new RSS10Standard();
        $dom = new \DOMDocument();
        $dom->appendChild($dom->createElement('channel'));
        $dom->appendChild($dom->createElementNS(RSS10Standard::NAMESPACE_URI, 'RDF'));
        $this->assertEquals(true, $standard->canHandle($dom));
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Invalid number of <RDF> tags: 0
     */
    public function testFailWhenNoRootIsFound()
    {
        $standard = new RSS10Standard();
        $dom = new \DOMDocument();
        $dom->appendChild($dom->createElement('feed'));
        $standard->getRootNode($dom);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Invalid number of <RDF> tags: 2
     */
    public function testFailWhenToManyRootsAreFound()
    {
        $standard = new RSS10Standard();
        $dom = new \DOMDocument();
        $dom->appendChild($dom->createElementNS(RSS10Standard::NAMESPACE_URI, 'RDF'));
        $dom->appendChild($dom->createElementNS(RSS10Standard::NAMESPACE_URI, 'RDF'));
        $standard->getRootNode($dom);
    }

    public function testSucceedWhenThereSOnlyOneRoot()
    {
        $standard = new RSS10Standard();
        $dom = new \DOMDocument();
        $root = $dom->createElementNS(RSS10Standard::NAMESPACE_URI, 'RDF');
        $dom->appendChild($root);
        $this->assertEquals($root, $standard->getRootNode($dom));
    }

}
