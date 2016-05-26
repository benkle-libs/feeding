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

namespace Benkle\Feeding;


use Benkle\Feeding\Interfaces\FeedInterface;
use Benkle\Feeding\Interfaces\NodeInterface;
use Benkle\Feeding\Interfaces\RuleInterface;
use Benkle\Feeding\Interfaces\StandardInterface;

/**
 * Class Parser
 * Our basic "parser". It walks over the dom document and applies the rules of a standards ruleset to the nodes.
 * @package Benkle\Feeding
 */
class Parser
{

    /** @var  StandardInterface */
    private $standard;

    /**
     * Parser constructor.
     * @param StandardInterface $standard
     */
    public function __construct(StandardInterface $standard)
    {
        $this->standard = $standard;
    }

    /**
     * Get the standard this parser was created for.
     * @return StandardInterface
     */
    public function getStandard()
    {
        return $this->standard;
    }

    /**
     * Turn a dom document into a feed object.
     * @param \DOMDocument $dom
     * @return FeedInterface
     */
    public function parse(\DOMDocument $dom)
    {
        $feed = $this->getStandard()->newFeed();
        $rootNode = $this->getStandard()->getRootNode($dom);
        $this->parseNodeChildren($rootNode, $feed);
        return $feed;
    }

    /**
     * Parse the children of a given node.
     * @param \DOMNode $node
     * @param NodeInterface $target
     */
    public function parseNodeChildren(\DOMNode $node, NodeInterface $target)
    {
        $rules = $this->getStandard()->getRules();
        foreach ($node->childNodes as $childNode) {
            /** @var RuleInterface $rule */
            foreach ($rules as $rule) {
                if ($rule->canHandle($childNode, $target)) {
                    $rule->handle($this, $childNode, $target);
                    break;
                }
            }
        }
    }

}
