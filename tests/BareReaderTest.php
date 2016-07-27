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


use Benkle\Feeding\Interfaces\DOMParserInterface;
use Benkle\Feeding\Interfaces\StandardInterface;
use Benkle\Feeding\Utilities\PriorityList;

class BareReaderTest extends \PHPUnit_Framework_TestCase
{

    public function testNewBasicReader()
    {
        $reader = new BareReader();
        $this->assertInstanceOf(BareReader::class, $reader);
    }

    public function testGetStandards()
    {
        $reader = new BareReader();
        $this->assertInstanceOf(PriorityList::class, $reader->getStandards());
    }

    public function testSetAndGetDOMParser()
    {
        $reader = new BareReader();
        $parser = $this->getMock(DOMParserInterface::class);
        $this->assertEquals($reader, $reader->setDomParser($parser));
        $this->assertEquals($parser, $reader->getDomParser());
    }

    /**
     * @expectedException \Benkle\Feeding\Exceptions\UnknownFeedFormatException
     * @expectedExceptionMessage Unknown feed format
     */
    public function testReadWithoutStandards()
    {
        $reader = new BareReader();
        $parser = $this->getMock(DOMParserInterface::class);
        $parser
            ->expects($this->exactly(1))
            ->method('parse')
            ->with('test')
            ->willReturn(new \DOMDocument());
        $reader->setDomParser($parser);

        $reader->read('test');
    }

    public function testReadWithStandards()
    {
        $reader = new BareReader();
        $parser = $this->getMock(DOMParserInterface::class);
        $parser
            ->expects($this->exactly(1))
            ->method('parse')
            ->with('test')
            ->willReturn(new \DOMDocument());
        $reader->setDomParser($parser);

        $standard = $this->getMock(StandardInterface::class);
        $standard
            ->expects($this->atLeast(1))
            ->method('canHandle')
            ->willReturn(false);
        $reader->getStandards()->add($standard, 10);

        $parser = $this
            ->getMockBuilder(Parser::class)
            ->disableOriginalConstructor()
            ->getMock();
        $parser
            ->expects($this->exactly(1))
            ->method('parse')
            ->willReturn('test');
        $standard = $this->getMock(StandardInterface::class);
        $standard
            ->expects($this->atLeast(1))
            ->method('canHandle')
            ->willReturn(true);
        $standard
            ->expects($this->exactly(1))
            ->method('getParser')
            ->willReturn($parser);
        $reader->getStandards()->add($standard, 10);

        $this->assertEquals('test', $reader->read('test'));
    }

}
