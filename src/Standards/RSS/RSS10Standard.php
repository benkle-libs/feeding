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

namespace Benkle\Feeding\Standards\RSS;

use Benkle\Feeding\Exceptions\InvalidNumberOfRootTagsException;

/**
 * Class RSS10Standard
 * Standard for handling RSS 1.0
 * @package Benkle\Feeding\Standards\RSS
 */
class RSS10Standard extends RSS09Standard
{
    const NAMESPACE_URI = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#';

    /**
     * Get the feed root from a dom document.
     * @return \DOMNode
     */
    public function getRootNode(\DOMDocument $dom)
    {
        $rootNodes = $dom->getElementsByTagNameNS(self::NAMESPACE_URI, 'RDF');
        if ($rootNodes->length != 1) {
            Throw new InvalidNumberOfRootTagsException('RDF', $rootNodes->length);
        }
        return $rootNodes->item(0);
    }

    /**
     * Check if a dom document is a feed this standard handles.
     * @param \DOMDocument $dom
     * @return bool
     */
    public function canHandle(\DOMDocument $dom)
    {
        return
            $dom->getElementsByTagNameNS(self::NAMESPACE_URI, 'RDF')->length == 1 &&
            $dom->getElementsByTagName('channel')->length == 1;
    }

}
