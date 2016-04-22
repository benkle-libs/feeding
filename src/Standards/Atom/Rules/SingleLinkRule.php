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


use Benkle\FeedParser\Interfaces\NodeInterface;
use Benkle\FeedParser\Interfaces\RuleInterface;
use Benkle\FeedParser\Parser;
use Benkle\FeedParser\Standards\Atom\Atom10Standard;

/**
 * Class SingleLinkRule
 * Tag a specific relation link and feed it to a setter.
 * @package Benkle\FeedParser\Standards\Atom\Rules
 */
class SingleLinkRule implements RuleInterface
{

    /** @var string  */
    private $rel = '';

    /** @var string  */
    private $setterName = '';

    /**
     * SingleLinkRule constructor.
     * @param string $rel
     * @param string $setterName
     */
    public function __construct($rel, $setterName)
    {
        $this->rel = strtolower($rel);
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
            strtolower($node->localName) == 'link' &&
            $node->namespaceURI == Atom10Standard::NAMESPACE_URI &&
            strtolower($node->attributes->getNamedItem('rel')->nodeValue) == $this->rel;
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
        $target->{$this->setterName}($node->attributes->getNamedItem('href')->nodeValue);
    }
}
