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
 * Interface ChannelInterface
 * The Channel interface bundles all properties that exists for both feeds and feed items.
 * @package Benkle\Feeding\Interfaces
 */
interface ChannelInterface extends NodeInterface
{
    /**
     * Get the title.
     * @return string
     */
    public function getTitle();

    /**
     * Set the title.
     * @param string $title
     * @return $this
     */
    public function setTitle($title);

    /**
     * Get the public id.
     * @return string
     */
    public function getPublicId();

    /**
     * Set the public id.
     * @param string $publicId
     * @return $this
     */
    public function setPublicId($publicId);

    /**
     * Get the description.
     * @return string
     */
    public function getDescription();

    /**
     * Set the description.
     * @param string $description
     * @return $this
     */
    public function setDescription($description);

    /**
     * Get the time of last modification.
     * @return \DateTime
     */
    public function getLastModified();

    /**
     * Set the time of last modification.
     * @param \DateTime $datetime
     * @return $this
     */
    public function setLastModified(\DateTime $lastModified);

    /**
     * Get the (canonical) link.
     * @return string
     */
    public function getLink();

    /**
     * Set the (canonical) link.
     * @param string $link
     * @return $this
     */
    public function setLink($link);
}
