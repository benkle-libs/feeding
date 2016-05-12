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

class SingleLinkRuleTest extends \PHPUnit_Framework_TestCase
{

    public function testNewRule()
    {
        $rule = new SingleLinkRule('', '');
        $this->assertInstanceOf(SingleLinkRule::class, $rule);
    }

    public function testCanHandle()
    {
        $rule = new SingleLinkRule('test', '');
        $dom = new \DOMDocument();
        $domNode1 = $this->createLinkTag($dom, 'test', 'localhost');
        $domNode2 = $this->createLinkTag($dom, 'tset', 'localhost');
        $node = $this->getMock(NodeInterface::class);

        $this->assertEquals(true, $rule->canHandle($domNode1, $node));
        $this->assertEquals(false, $rule->canHandle($domNode2, $node));
    }

    public function testHandle()
    {
        $rule = new SingleLinkRule('test', 'setLink');
        $dom = new \DOMDocument();
        $domNode = $this->createLinkTag($dom, 'test', 'localhost');
        $parser = $this
            ->getMockBuilder(Parser::class)
            ->disableOriginalConstructor()
            ->getMock();
        $channel = $this->getMock(ChannelInterface::class);
        $channel
            ->expects($this->atLeast(1))
            ->method('setLink')
            ->with('localhost');

        $rule->handle($parser, $domNode, $channel);
    }

    private function createLinkTag(\DOMDocument $dom, $rel, $href)
    {
        $node = $dom->createElementNS(Atom10Standard::NAMESPACE_URI, 'link');
        $relAttr = $dom->createAttribute('rel');
        $relAttr->nodeValue = $rel;
        $node->appendChild($relAttr);
        $hrefAttr = $dom->createAttribute('href');
        $hrefAttr->nodeValue = $href;
        $node->appendChild($hrefAttr);
        return $node;
    }
}
