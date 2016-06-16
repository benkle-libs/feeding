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

namespace Benkle\Feeding\Traits;


use Benkle\Feeding\Interfaces\EnclosureInterface;

trait WithEnclosuresTrait
{
    private $enclosures = [];

    /**
     * Add an enclosure to the item.
     * @param EnclosureInterface $enclosure
     * @return $this
     */
    public function addEnclosure(EnclosureInterface $enclosure)
    {
        $this->enclosures[] = $enclosure;
        return $this;
    }

    /**
     * Remove an enclosure from the feed.
     * @param EnclosureInterface $enclosure
     * @return $this
     */
    public function removeEnclosure(EnclosureInterface $enclosure)
    {
        foreach ($this->enclosures as $i => $itemEnclosure) {
            if ($itemEnclosure == $enclosure) {
                unset($this->enclosures[$i]);
            }
        }
        $this->enclosures = array_values($this->enclosures);
        return $this;
    }

    /**
     * Get all enclosures.
     * @return EnclosureInterface[]
     */
    public function getEnclosures()
    {
        return $this->enclosures;
    }

}
