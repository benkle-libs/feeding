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

namespace Benkle\Feeding\Utilities;
use Benkle\Feeding\Exceptions\InvalidObjectClassException;

/**
 * Class PriorityList
 * This class is for prioritised lists.
 * It might be moved to another package in the future.
 * @package Benkle\Feeding\Utilities
 */
class PriorityList implements \Iterator
{
    const DEFAULT_PRIORITY = 50;

    private $entries = [];
    private $position = 0;
    private $sorted = false;
    /* This is for a little "safety", until we get Generics */
    private $entryClass = null;

    /**
     * PriorityList constructor.
     * @param null $entryClass
     */
    public function __construct($entryClass = null)
    {
        $this->entryClass = $entryClass;
    }


    /**
     * Add an entry to the list.
     * @param mixed $entry
     * @param int $priority
     * @return $this
     * @throws InvalidObjectClassException
     */
    public function add($entry, $priority = self::DEFAULT_PRIORITY)
    {
        if (isset($this->entryClass) && !($entry instanceof $this->entryClass)) {
            throw new InvalidObjectClassException($this->entryClass, get_class($entry));
        }
        $this->entries[] = ['data' => $entry, 'priority' => $priority];
        $this->sorted = false;
        return $this;
    }

    /**
     * Removes an entry from the list.
     * @param mixed $entry
     * @param bool|int $priority When false, remove all instances of $entry from the list, otherwise only those with matching priority
     * @return $this
     */
    public function remove($entry, $priority = false)
    {
        $unsetAnything = false;
        foreach ($this->entries as $i => $entryData) {
            if ($entry === $entryData['data'] && ($priority === false || $entryData['priority'] == $priority)) {
                unset($this->entries[$i]);
                $unsetAnything = true;
            }
        }
        if ($unsetAnything) {
            $this->sorted = false;
        }
        return $this;
    }

    /**
     * change the priority for an entry.
     * @param mixed $entry
     * @param int $newPriority
     * @param bool|int $oldPriority If falsechange remove all instances of $entry from the list, otherwise only those with matching priority
     * @return $this
     */
    public function reprioritise($entry, $newPriority, $oldPriority = false)
    {
        $changedAnything = false;
        foreach ($this->entries as $i => &$entryData) {
            if ($entry === $entryData['data'] && ($oldPriority === false || $entryData['priority'] == $oldPriority)) {
                $entryData['priority'] = $newPriority;
                $changedAnything = true;
            }
        }
        if ($changedAnything) {
            $this->sorted = false;
        }
        return $this;
    }

    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current()
    {
        if (!$this->sorted) {
            $this->rewind();
        }
        return $this->entries[$this->position]['data'];
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next()
    {
        $this->position++;
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid()
    {
        return $this->sorted && isset($this->entries[$this->position]);
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        usort(
            $this->entries, function ($a, $b) {
            return $a['priority'] - $b['priority'];
        }
        );
        $this->position = 0;
        $this->sorted = true;
    }

    /**
     * Get the priority of the current entry.
     * @return int
     */
    public function currentPriority()
    {
        if (!$this->sorted) {
            $this->rewind();
        }
        return $this->entries[$this->position]['priority'];
    }

    /**
     * Returns all items, ordered by priority.
     * @return array
     */
    public function toArray()
    {
        if (!$this->sorted) {
            $this->rewind();
        }
        return array_map(
            function ($entry) {
                return $entry['data'];
            }, $this->entries
        );
    }
}
