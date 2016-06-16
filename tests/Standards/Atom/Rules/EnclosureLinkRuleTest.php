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
use Benkle\Feeding\Interfaces\FeedInterface;
use Benkle\Feeding\Interfaces\ItemInterface;
use Benkle\Feeding\Interfaces\RuleInterface;
use Benkle\Feeding\Parser;
use Benkle\Feeding\Standards\Atom\Atom10Standard;

class EnclosureLinkRuleTest extends \PHPUnit_Framework_TestCase
{

    public function testNewRule()
    {
        $rule = new EnclosureLinkRule();
        $this->assertInstanceOf(RuleInterface::class, $rule);
    }

    public function testCanHandle()
    {
        $rule = new EnclosureLinkRule();
        $dom = new \DOMDocument();
        $item = $this->getMock(ItemInterface::class);

        $domNode = $this->createLinkTag($dom, 'enclosure', 'localhost');
        $this->assertEquals(true, $rule->canHandle($domNode, $item));

        $domNode = $this->createLinkTag($dom, 'enclosure', 'localhost', true);
        $this->assertEquals(true, $rule->canHandle($domNode, $item));

        $domNode = $this->createLinkTag($dom, 'alternate', 'localhost');
        $this->assertEquals(false, $rule->canHandle($domNode, $item));

        $item = $this->getMock(ChannelInterface::class);

        $domNode = $this->createLinkTag($dom, 'enclosure', 'localhost');
        $this->assertEquals(false, $rule->canHandle($domNode, $item));
    }

    public function testHandle()
    {
        $rule = new EnclosureLinkRule();
        $dom = new \DOMDocument();
        $domNode = $this->createLinkTag($dom, 'enclosure', 'localhost');
        $hrefAttr = $dom->createAttribute('length');
        $hrefAttr->nodeValue = '' . rand(400, 5000);
        $domNode->appendChild($hrefAttr);
        $hrefAttr = $dom->createAttribute('type');
        $hrefAttr->nodeValue = uniqid();
        $domNode->appendChild($hrefAttr);

        $parser = $this
            ->getMockBuilder(Parser::class)
            ->disableOriginalConstructor()
            ->getMock();
        $item = $this->getMock(ItemInterface::class);
        $item
            ->expects($this->atLeast(1))
            ->method('addEnclosure');

        $rule->handle($parser, $domNode, $item);
    }

    private function createLinkTag(\DOMDocument $dom, $rel, $href, $upperCase = false)
    {
        $node = $dom->createElementNS(Atom10Standard::NAMESPACE_URI, $upperCase ? 'LINK' : 'link');
        $relAttr = $dom->createAttribute('rel');
        $relAttr->nodeValue = $rel;
        $node->appendChild($relAttr);
        $hrefAttr = $dom->createAttribute('href');
        $hrefAttr->nodeValue = $href;
        $node->appendChild($hrefAttr);
        return $node;
    }
}
