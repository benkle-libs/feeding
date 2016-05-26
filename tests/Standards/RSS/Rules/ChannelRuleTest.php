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


use Benkle\Feeding\Interfaces\FeedInterface;
use Benkle\Feeding\Interfaces\ItemInterface;
use Benkle\Feeding\Interfaces\RuleInterface;
use Benkle\Feeding\Parser;

class ChannelRuleTest extends \PHPUnit_Framework_TestCase
{

    public function testNewRule()
    {
        $rule = new ChannelRule();
        $this->assertInstanceOf(RuleInterface::class, $rule);
    }

    public function testCanHandle()
    {
        $rule = new ChannelRule();
        $dom = new \DOMDocument();
        $feed = $this->getMock(FeedInterface::class);
        $item = $this->getMock(ItemInterface::class);

        $domNode = $dom->createElement('channel');
        $this->assertEquals(true, $rule->canHandle($domNode, $feed));
        $this->assertEquals(false, $rule->canHandle($domNode, $item));

        $domNode = $dom->createElement('CHANNEL');
        $this->assertEquals(true, $rule->canHandle($domNode, $feed));
        $this->assertEquals(false, $rule->canHandle($domNode, $item));

        $domNode = $dom->createElement('not-channel');
        $this->assertEquals(false, $rule->canHandle($domNode, $feed));
        $this->assertEquals(false, $rule->canHandle($domNode, $item));
    }

    public function testHandle()
    {
        $rule = new ChannelRule();
        $dom = new \DOMDocument();
        $domNode = $dom->createElement('channel');
        $feed = $this->getMock(FeedInterface::class);
        $parser = $this
            ->getMockBuilder(Parser::class)
            ->disableOriginalConstructor()
            ->getMock();
        $parser
            ->expects($this->exactly(1))
            ->method('parseNodeChildren')
            ->with($domNode, $feed);

        $rule->handle($parser, $domNode, $feed);
    }

}
