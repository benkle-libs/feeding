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

namespace Benkle\Feeding\Exceptions;

/**
 * Class InvalidObjectClassException
 * Gets thrown when a priority list is feed the wrong class.
 * @package Benkle\Feeding\Exceptions
 */
class InvalidObjectClassException extends Exception
{
    private $rightClass = '';
    private $wrongClass = '';

    /**
     * InvalidObjectClassException constructor.
     * @param string $rightClass
     * @param string $wrongClass
     */
    public function __construct($rightClass, $wrongClass)
    {
        parent::__construct(sprintf('List was limited to "%s", but was given a "%s"', $rightClass, $wrongClass), -77);
        $this->rightClass = $rightClass;
        $this->wrongClass = $wrongClass;
    }

    /**
     * Get the class name of the expected class.
     * @return string
     */
    public function getRightClass()
    {
        return $this->rightClass;
    }

    /**
     * Get the class name of the class that was feed in.
     * @return string
     */
    public function getWrongClass()
    {
        return $this->wrongClass;
    }

}
