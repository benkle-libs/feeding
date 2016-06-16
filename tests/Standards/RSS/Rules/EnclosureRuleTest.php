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
use Benkle\Feeding\Interfaces\ItemInterface;
use Benkle\Feeding\Interfaces\RuleInterface;
use Benkle\Feeding\Parser;

class EnclosureRuleTest extends \PHPUnit_Framework_TestCase
{

    public function testNewRule()
    {
        $rule = new EnclosureRule();
        $this->assertInstanceOf(RuleInterface::class, $rule);
    }

    public function testCanHandle()
    {
        $rule = new EnclosureRule();
        $dom = new \DOMDocument();
        $item = $this->getMock(ItemInterface::class);

        $domNode = $this->createEnclosureTag($dom, 'unknown', 'localhost', 0);
        $this->assertEquals(true, $rule->canHandle($domNode, $item));

        $domNode = $this->createEnclosureTag($dom, 'unknown', 'localhost', 0, false, true);
        $this->assertEquals(true, $rule->canHandle($domNode, $item));

        $item = $this->getMock(ChannelInterface::class);

        $domNode = $this->createEnclosureTag($dom, 'unknown', 'localhost', 0);
        $this->assertEquals(false, $rule->canHandle($domNode, $item));
    }

    public function testHandle()
    {
        $rule = new EnclosureRule();
        $dom = new \DOMDocument();
        $domNode = $this->createEnclosureTag($dom, 'unknown', 'localhost', rand(400, 5000));

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

    private function createEnclosureTag(\DOMDocument $dom, $type, $url, $length, $title = false, $upperCase = false)
    {
        $node = $dom->createElement($upperCase ? 'ENCLOSURE' : 'enclosure');
        $typeAttr = $dom->createAttribute('type');
        $typeAttr->nodeValue = $type;
        $node->appendChild($typeAttr);
        $urlAttr = $dom->createAttribute('url');
        $urlAttr->nodeValue = $url;
        $node->appendChild($urlAttr);
        $lengthAttr = $dom->createAttribute('length');
        $lengthAttr->nodeValue = '' . $length;
        $node->appendChild($lengthAttr);
        if ($title) {
            $titleAttr = $dom->createAttribute('title');
            $titleAttr->nodeValue = $title;
            $node->appendChild($titleAttr);
        }
        return $node;
    }
}
