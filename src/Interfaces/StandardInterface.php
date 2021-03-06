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

namespace Benkle\Feeding\Interfaces;


use Benkle\Feeding\Exceptions\InvalidNumberOfRootTagsException;
use Benkle\Feeding\Parser;
use Benkle\Feeding\Utilities\PriorityList;

/**
 * Interface StandardInterface
 * A Standard is a combination of rule set, feed class factory and feed identification.
 * @package Benkle\Feeding\Interfaces
 */
interface StandardInterface
{
    /**
     * Get the standards rule set.
     * @return PriorityList
     */
    public function getRules();

    /**
     * Get a new feed object.
     * @return FeedInterface
     */
    public function newFeed();

    /**
     * Get the feed root from a dom document.
     * @return \DOMNode
     * @throws InvalidNumberOfRootTagsException
     */
    public function getRootNode(\DOMDocument $dom);

    /**
     * Get a parser for this standard.
     * @return Parser
     */
    public function getParser();

    /**
     * Check if a dom document is a feed this standard handles.
     * @param \DOMDocument $dom
     * @return bool
     */
    public function canHandle(\DOMDocument $dom);
}
