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

namespace Benkle\Feeding\Standards\RSS\Rules;


use Benkle\Feeding\Interfaces\ChannelInterface;
use Benkle\Feeding\Interfaces\NodeInterface;
use Benkle\Feeding\Interfaces\RuleInterface;
use Benkle\Feeding\Parser;

class LinkRuleTest extends \PHPUnit_Framework_TestCase
{

    public function testNewRule()
    {
        $rule = new LinkRule('', '');
        $this->assertInstanceOf(RuleInterface::class, $rule);
    }

    public function testCanHandle()
    {
        $rule = new LinkRule();
        $dom = new \DOMDocument();
        $channel = $this->getMock(ChannelInterface::class);
        $node = $this->getMock(NodeInterface::class);

        $domNode = $this->createLinkTag($dom, 'localhost');
        $this->assertEquals(true, $rule->canHandle($domNode, $channel));
        $this->assertEquals(false, $rule->canHandle($domNode, $node));

        $domNode = $this->createLinkTag($dom, 'localhost', true);
        $this->assertEquals(true, $rule->canHandle($domNode, $channel));
        $this->assertEquals(false, $rule->canHandle($domNode, $node));

        $domNode = $dom->createElement('not-link');
        $this->assertEquals(false, $rule->canHandle($domNode, $channel));
        $this->assertEquals(false, $rule->canHandle($domNode, $node));
    }

    public function testHandle()
    {
        $rule = new LinkRule();
        $dom = new \DOMDocument();
        $domNode = $this->createLinkTag($dom, 'localhost');
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

    public function testHandleForFedUpFeeds()
    {
        $rule = new LinkRule();
        $dom = new \DOMDocument();
        $domNode = $dom->createElement('link');
        $dom->appendChild($domNode);
        $domValue = $dom->createTextNode('localhost');
        $dom->appendChild($domValue);
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

    private function createLinkTag(\DOMDocument $dom, $href, $upperCase = false)
    {
        $node = $dom->createElement($upperCase? 'LINK' : 'link');
        $node->nodeValue = $href;
        return $node;
    }

}
