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
use Benkle\FeedParser\Interfaces\RuleInterface;
use Benkle\FeedParser\Parser;
use Benkle\FeedParser\Standards\Atom\Atom10Standard;

/**
 * Class SimpleAtomFieldRule
 * A simple catch-any for all of those simpler Atom elements.
 * @package Benkle\FeedParser\Standards\Atom\Rules
 */
class SimpleAtomFieldRule implements RuleInterface
{

    /** @var string  */
    private $nodeName = '';

    /** @var string  */
    private $setterName = '';

    /**
     * SimpleAtomFieldRule constructor.
     * @param string $nodeName
     * @param string $setterName
     */
    public function __construct($nodeName, $setterName)
    {
        $this->nodeName = strtolower($nodeName);
        $this->setterName = $setterName;
    }

    /**
     * Check if a dom node can be handled by this rule.
     * @param \DOMNode $node
     * @param NodeInterface $target
     * @return bool
     */
    public function canHandle(\DOMNode $node, NodeInterface $target)
    {
        return
            strtolower($node->localName) == $this->nodeName &&
            $node->namespaceURI == Atom10Standard::NAMESPACE_URI &&
            $target instanceof ChannelInterface;
    }

    /**
     * Handle a dom node.
     * @param Parser $parser
     * @param \DOMNode $node
     * @param NodeInterface $target
     * @return void
     */
    public function handle(Parser $parser, \DOMNode $node, NodeInterface $target)
    {
        $target->{$this->setterName}($this->getNodeContent($node));
    }

    /**
     * Enable XHTML types.
     * @param \DOMNode $node
     * @return string
     * @codeCoverageIgnore
     */
    private function getNodeContent(\DOMNode $node)
    {
        $type = $node->attributes->getNamedItem('type');
        if ($type && strtolower($type->nodeValue) == 'xhtml') {
            $result = '';
            foreach ($node->childNodes as $childNode) {
                $result .= $node->ownerDocument->save($childNode);
            }
            return $result;
        } else {
            return $node->nodeValue;
        }
    }
}
