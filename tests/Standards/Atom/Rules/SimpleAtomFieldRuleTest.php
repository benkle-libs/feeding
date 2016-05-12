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

namespace Benkle\FeedParser\Standards\Atom\Rules;


use Benkle\FeedParser\Interfaces\ChannelInterface;
use Benkle\FeedParser\Interfaces\NodeInterface;
use Benkle\FeedParser\Parser;
use Benkle\FeedParser\Standards\Atom\Atom10Standard;

class SimpleAtomFieldRuleTest extends \PHPUnit_Framework_TestCase
{

    public function testNewRule()
    {
        $rule = new SimpleAtomFieldRule('', '');
        $this->assertInstanceOf(SimpleAtomFieldRule::class, $rule);
    }

    public function testCanHandle()
    {
        $rule = new SimpleAtomFieldRule('test', '');
        $dom = new \DOMDocument();
        $domNode = $dom->createElementNS(Atom10Standard::NAMESPACE_URI, 'test');
        $channel = $this->getMock(ChannelInterface::class);
        $node = $this->getMock(NodeInterface::class);

        $this->assertEquals(true, $rule->canHandle($domNode, $channel));
        $this->assertEquals(false, $rule->canHandle($domNode, $node));

        $domNode = $dom->createElementNS(Atom10Standard::NAMESPACE_URI, 'tset');
        
        $this->assertEquals(false, $rule->canHandle($domNode, $channel));
        $this->assertEquals(false, $rule->canHandle($domNode, $node));
    }

    public function testHandle()
    {
        $rule = new SimpleAtomFieldRule('test', 'setTitle');
        $dom = new \DOMDocument();
        $domNode = $dom->createElementNS(Atom10Standard::NAMESPACE_URI, 'test');
        $domNode->nodeValue = 'Lorem ipsum';
        $parser = $this
            ->getMockBuilder(Parser::class)
            ->disableOriginalConstructor()
            ->getMock();
        $channel = $this->getMock(ChannelInterface::class);
        $channel
            ->expects($this->atLeast(1))
            ->method('setTitle')
            ->with($domNode->nodeValue);

        $rule->handle($parser, $domNode, $channel);
    }
}
