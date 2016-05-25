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
use Psr\Log\LoggerInterface;

class FallbackStackParserTest extends \PHPUnit_Framework_TestCase
{

    public function testNewDOMParser()
    {
        $parser = new FallbackStackParser();
        $this->assertInstanceOf(DOMParserInterface::class, $parser);
    }

    public function testNewDOMParserFromFactory()
    {
        $parser = FallbackStackParser::create([]);
        $this->assertInstanceOf(DOMParserInterface::class, $parser);
    }

    public function testParse()
    {
        $parsers = [];

        $parser = $this->getMock(DOMParserInterface::class);
        $parser
            ->expects($this->exactly(1))
            ->method('parse')
            ->with('test')
            ->willReturn(false);
        $parsers[] = $parser;

        $parser = $this->getMock(DOMParserInterface::class);
        $parser
            ->expects($this->exactly(1))
            ->method('parse')
            ->with('test')
            ->willReturn(new \DOMDocument());
        $parsers[] = $parser;

        $parser = FallbackStackParser::create($parsers);
        $parser->parse('test');
    }

    /**
     * @expectedException \DOMException
     * @expectedExceptionMessage Unable to parse source
     */
    public function testParseWithoutCapableParsers()
    {
        $parser = FallbackStackParser::create([]);
        $parser->parse('test');
    }

    /**
     * @expectedException \DOMException
     * @expectedExceptionMessage Unable to parse source
     */
    public function testParseWithException()
    {
        $parser = $this->getMock(DOMParserInterface::class);
        $parser
            ->expects($this->exactly(1))
            ->method('parse')
            ->with('test')
            ->willThrowException(new \Exception('test'));
        $parsers[] = $parser;

        $parser = FallbackStackParser::create($parsers);
        $logger = $this->getMock(LoggerInterface::class);
        $logger
            ->expects($this->exactly(1))
            ->method('notice')
            ->withAnyParameters()
            ->willReturnCallback(
                function ($message, array $context = []) {
                    $this->assertEquals('test', $message);
                    $this->assertArrayHasKey('code', $context);
                    $this->assertArrayHasKey('file', $context);
                    $this->assertArrayHasKey('line', $context);
                }
            );
        $parser->setLogger($logger);
        $parser->parse('test');
    }

}
