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


use Benkle\FeedParser\Interfaces\ChannelInterface;
use Benkle\FeedParser\Interfaces\FeedInterface;
use Benkle\FeedParser\Interfaces\NodeInterface;
use Benkle\FeedParser\Interfaces\RuleInterface;
use Benkle\FeedParser\Interfaces\StandardInterface;
use Benkle\FeedParser\Utilities\PriorityList;

class ParserTest extends \PHPUnit_Framework_TestCase
{

    public function testNewParser()
    {
        $standard = $this->getMock(StandardInterface::class);
        $parser = new Parser($standard);
        $this->assertInstanceOf(Parser::class, $parser);
        return $parser;
    }

    /**
     * @depends testNewParser
     * @param Parser $parser
     */
    public function testGetStandard(Parser $parser)
    {
        $this->assertInstanceOf(StandardInterface::class, $parser->getStandard());
    }

    /**
     * @depends testNewParser
     * @param Parser $parser
     */
    public function testParseNodeChildren(Parser $parser)
    {
        $dom = new \DOMDocument();
        $node = $dom->createElement('test');
        $node2 = $dom->createElement('test2');
        $node->appendChild($node2);
        $node3 = $dom->createElement('test3');
        $node->appendChild($node3);

        $target = $this->getMock(ChannelInterface::class);

        $rules = new PriorityList(RuleInterface::class);
        $rule = $this->getMock(RuleInterface::class);
        $rule
            ->expects($this->atLeast(1))
            ->method('canHandle')
            ->will(
                $this->returnCallback(
                    function (\DOMNode $node, NodeInterface $target) use ($node2) {
                        return $node === $node2;
                    }
                )
            );
        $rule
            ->expects($this->exactly(1))
            ->method('handle')
            ->will(
                $this->returnCallback(
                    function (Parser $parserIn, \DOMNode $nodeIn, NodeInterface $targetIn) use ($node2, $parser, $target) {
                        $this->assertEquals($parser, $parserIn);
                        $this->assertEquals($node2, $nodeIn);
                        $this->assertEquals($target, $targetIn);
                    }
                )
            );
        $rules->add($rule);

        $rule2 = $this->getMock(RuleInterface::class);
        $rule2->expects($this->atLeast(1))->method('canHandle')->willReturn(false);
        $rule2->expects($this->exactly(0))->method('handle');
        $rules->add($rule2);

        /** @var \PHPUnit_Framework_MockObject_MockObject $standard */
        $standard = $parser->getStandard();
        $standard
            ->expects($this->atLeast(1))
            ->method('getRules')
            ->willReturn($rules);

        $parser->parseNodeChildren($node, $target);

        return $parser;
    }

    /**
     * @param Parser $parser
     * @depends testParseNodeChildren
     */
    public function testParse(Parser $parser)
    {
        $dom = new \DOMDocument();
        $root = $dom->createElement('root');

        $feed = $this->getMock(FeedInterface::class);

        /** @var \PHPUnit_Framework_MockObject_MockObject $standard */
        $standard = $parser->getStandard();
        $standard
            ->expects($this->exactly(1))
            ->method('getRootNode')
            ->willReturn($root);
        $standard
            ->expects($this->exactly(1))
            ->method('newFeed')
            ->willReturn($feed);

        $this->assertEquals($feed, $parser->parse($dom));
    }

}
