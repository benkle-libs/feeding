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

/**
 * Interface EnclosureInterface
 * This interface represents an enclosure (you know, for podcasts...)
 * @package Benkle\Feeding\Interfaces
 */
interface EnclosureInterface extends NodeInterface
{
    /**
     * Get the enclosure url.
     * @return string
     */
    public function getUrl();

    /**
     * Set the enclosure url.
     * @param string $url
     * @return $this
     */
    public function setUrl($url);

    /**
     * Get the enclosure mimetype.
     * @return string
     */
    public function getType();

    /**
     * Set the enclosure mimetype.
     * @param string $type
     * @return $this
     */
    public function setType($type);

    /**
     * Get the enclosure title.
     * @return string
     */
    public function getTitle();

    /**
     * Set the enclosure title.
     * @param string $title
     * @return $this
     */
    public function setTitle($title);

    /**
     * Get the enclosure length.
     * @return int
     */
    public function getLength();

    /**
     * Set the enclosure length.
     * @param int $length
     * @return $this
     */
    public function setLength($length);

}
