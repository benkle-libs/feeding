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


use Benkle\Feeding\Exceptions\UnknownFeedFormatException;
use Benkle\Feeding\Interfaces\DOMParserInterface;
use Benkle\Feeding\Interfaces\StandardInterface;
use Benkle\Feeding\Utilities\PriorityList;

/**
 * Class BareReader
 * This basic reader class handles parsing of source strings into a DOMDocument and selecting the right standard to
 * produce a feed object.
 * @package Benkle\Feeding
 */
class BareReader
{
    /** @var DOMParserInterface */
    private $domParser;

    /** @var PriorityList */
    private $standards;

    /**
     * BareReader constructor.
     */
    public function __construct()
    {
        $this->standards = new PriorityList(StandardInterface::class);
    }

    /**
     * Get the set of parsing standards for this reader.
     * @return PriorityList
     */
    public function getStandards()
    {
        return $this->standards;
    }

    /**
     * Get the dom parser.
     * @return DOMParserInterface
     */
    public function getDomParser()
    {
        return $this->domParser;
    }

    /**
     * Set the dom parser.
     * @param DOMParserInterface $domParser
     * @return $this
     */
    public function setDomParser(DOMParserInterface $domParser)
    {
        $this->domParser = $domParser;
        return $this;
    }

    /**
     * Turn a source string into a feed object.
     * @param $source
     */
    public function read($source)
    {
        $dom = $this->getDomParser()->parse($source);
        /** @var StandardInterface $standard */
        foreach ($this->getStandards() as $standard) {
            if ($standard->canHandle($dom)) {
                return $standard->getParser()->parse($dom);
            }
        }
        throw new UnknownFeedFormatException();
    }

}
