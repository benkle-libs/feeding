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

namespace Benkle\Feeding\Standards\Atom\Rules;


use Benkle\Feeding\Interfaces\ChannelInterface;
use Benkle\Feeding\Interfaces\NodeInterface;
use Benkle\Feeding\Interfaces\RuleInterface;
use Benkle\Feeding\Parser;
use Benkle\Feeding\Standards\Atom\Atom10Standard;

class UpdatedRuleTest extends \PHPUnit_Framework_TestCase
{

    public function testNewRule()
    {
        $rule = new UpdatedRule();
        $this->assertInstanceOf(RuleInterface::class, $rule);
    }

    public function testCanHandle()
    {
        $rule = new UpdatedRule();
        $dom = new \DOMDocument();
        $channel = $this->getMock(ChannelInterface::class);
        $node = $this->getMock(NodeInterface::class);

        $domNode = $dom->createElementNS(Atom10Standard::NAMESPACE_URI, 'updated');
        $this->assertEquals(true, $rule->canHandle($domNode, $channel));
        $this->assertEquals(false, $rule->canHandle($domNode, $node));

        $domNode = $dom->createElementNS(Atom10Standard::NAMESPACE_URI, 'UPDATED');
        $this->assertEquals(true, $rule->canHandle($domNode, $channel));
        $this->assertEquals(false, $rule->canHandle($domNode, $node));

        $domNode = $dom->createElementNS(Atom10Standard::NAMESPACE_URI, 'not-updated');
        $this->assertEquals(false, $rule->canHandle($domNode, $channel));
        $this->assertEquals(false, $rule->canHandle($domNode, $node));
    }

    public function testHandle()
    {
        $rule = new UpdatedRule();
        $dom = new \DOMDocument();
        $domNode = $dom->createElementNS(Atom10Standard::NAMESPACE_URI, 'updated');
        $domNode->nodeValue = '2003-12-14T10:20:09Z';
        $parser = $this
            ->getMockBuilder(Parser::class)
            ->disableOriginalConstructor()
            ->getMock();
        $channel = $this->getMock(ChannelInterface::class);
        $channel
            ->expects($this->atLeast(1))
            ->method('setLastModified')
            ->with($this->isInstanceOf(\DateTime::class));

        $rule->handle($parser, $domNode, $channel);
    }

}
