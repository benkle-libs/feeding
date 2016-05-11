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

namespace Benkle\FeedParser\Utilities;


class PriorityListTest extends \PHPUnit_Framework_TestCase
{

    const ALPHA = 'Alpha';

    const BRAVO = 'Bravo';

    const CHARLIE = 'Charlie';

    public function testNewPriorityList()
    {
        $priorityList = new PriorityList();
        $this->assertInstanceOf(PriorityList::class, $priorityList);
        return $priorityList;
    }

    /**
     * @param $priorityList
     * @depends testNewPriorityList
     */
    public function testAdd(PriorityList $priorityList)
    {
        $this->assertEquals($priorityList, $priorityList->add(self::ALPHA), 'PriorityList::add does not return $this');
        $this->assertContains(self::ALPHA, $priorityList->toArray(), 'Test object was not put into priority list');
        return $priorityList;
    }

    /**
     * @param $priorityList
     * @depends testAdd
     */
    public function testAddWithPriority(PriorityList $priorityList)
    {
        $this->assertEquals($priorityList, $priorityList->add(self::BRAVO, $priorityList::DEFAULT_PRIORITY - 5), 'PriorityList::add does not return $this');
        $this->assertContains(self::BRAVO, $priorityList->toArray(), 'Test object was not put into priority list');
        return $priorityList;
    }

    /**
     * @param $priorityList
     * @depends testAddWithPriority
     */
    public function testOrdering(PriorityList $priorityList)
    {
        $this->assertEquals($priorityList, $priorityList->add(self::CHARLIE, $priorityList::DEFAULT_PRIORITY + 5), 'PriorityList::add does not return $this');
        $this->assertContains(self::CHARLIE, $priorityList->toArray(), 'Test object was not put into priority list');
        $this->assertEquals(
            [
                self::BRAVO,
                self::ALPHA,
                self::CHARLIE,
            ], $priorityList->toArray(), 'Test objects were returned in wrong order'
        );
        return $priorityList;
    }

    /**
     * @param $priorityList
     * @depends testOrdering
     */
    public function testRemove(PriorityList $priorityList)
    {
        $this->assertEquals($priorityList, $priorityList->add(self::CHARLIE, $priorityList::DEFAULT_PRIORITY + 5), 'PriorityList::add does not return $this');
        $this->assertEquals(
            [
                self::BRAVO,
                self::ALPHA,
                self::CHARLIE,
                self::CHARLIE,
            ], $priorityList->toArray(), 'Test objects were returned in wrong order'
        );
        $this->assertEquals($priorityList, $priorityList->remove(self::CHARLIE), 'PriorityList::remove does not return $this');
        $this->assertEquals(
            [
                self::BRAVO,
                self::ALPHA,
            ], $priorityList->toArray(), sprintf('All "%s" were not removed', self::CHARLIE)
        );
        return $priorityList;
    }

    /**
     * @param $priorityList
     * @depends testRemove
     */
    public function testRemoveWithPriority(PriorityList $priorityList)
    {
        $this->assertEquals($priorityList, $priorityList->add(self::BRAVO, $priorityList::DEFAULT_PRIORITY + 5), 'PriorityList::add does not return $this');
        $this->assertEquals(
            [
                self::BRAVO,
                self::ALPHA,
                self::BRAVO,
            ], $priorityList->toArray(), 'Test objects were returned in wrong order'
        );
        $this->assertEquals($priorityList, $priorityList->remove(self::BRAVO, $priorityList::DEFAULT_PRIORITY + 5), 'PriorityList::remove does not return $this');
        $this->assertEquals(
            [
                self::BRAVO,
                self::ALPHA,
            ], $priorityList->toArray(), sprintf('Last "%s" was not removed', self::BRAVO)
        );
        return $priorityList;
    }

    /**
     * @param $priorityList
     * @depends testRemoveWithPriority
     */
    public function testReprioritise(PriorityList $priorityList)
    {
        $this->assertEquals($priorityList, $priorityList->reprioritise(self::BRAVO, $priorityList::DEFAULT_PRIORITY + 5), 'PriorityList::reprioritise does not return $this');
        $this->assertEquals([self::ALPHA, self::BRAVO], $priorityList->toArray(), 'Items were not reordered');
        return $priorityList;
    }

    /**
     * @param $priorityList
     * @depends testReprioritise
     */
    public function testReprioritiseWithPriority(PriorityList $priorityList)
    {
        $this->assertEquals($priorityList, $priorityList->reprioritise(self::ALPHA, $priorityList::DEFAULT_PRIORITY + 5, 0), 'PriorityList::reprioritise does not return $this');
        $this->assertEquals([self::ALPHA, self::BRAVO], $priorityList->toArray(), 'Items were reordered');
        $this->assertEquals($priorityList, $priorityList->add(self::CHARLIE, 0), 'PriorityList::add does not return $this');
        $this->assertEquals(
            [
                self::CHARLIE,
                self::ALPHA,
                self::BRAVO,
            ], $priorityList->toArray(), 'Test objects were returned in wrong order'
        );
        $this->assertEquals($priorityList, $priorityList->reprioritise(self::CHARLIE, $priorityList::DEFAULT_PRIORITY + 10, 0), 'PriorityList::reprioritise does not return $this');
        $this->assertEquals(
            [
                self::ALPHA,
                self::BRAVO,
                self::CHARLIE,
            ], $priorityList->toArray(), 'Test objects were not reordered'
        );
        return $priorityList;
    }

    /**
     * @param PriorityList $priorityList
     * @depends testReprioritiseWithPriority
     */
    public function testIteration(PriorityList $priorityList)
    {
        $priorityList->rewind();
        $this->assertEquals(self::ALPHA, $priorityList->current());
        $this->assertEquals(0, $priorityList->key());
        $this->assertEquals(50, $priorityList->currentPriority());
        $this->assertEquals(true, $priorityList->valid());
        $priorityList->next();
        $this->assertEquals(self::BRAVO, $priorityList->current());
        $this->assertEquals(1, $priorityList->key());
        $this->assertEquals(55, $priorityList->currentPriority());
        $this->assertEquals(true, $priorityList->valid());
        $priorityList->next();
        $this->assertEquals(self::CHARLIE, $priorityList->current());
        $this->assertEquals(2, $priorityList->key());
        $this->assertEquals(60, $priorityList->currentPriority());
        $this->assertEquals(true, $priorityList->valid());
        $priorityList->next();
        $this->assertEquals(false, $priorityList->valid());
        return $priorityList;
    }

    /**
     * @param PriorityList $priorityList
     * @depends testIteration
     */
    public function testIterationAfterInsertion(PriorityList $priorityList)
    {
        $priorityList->rewind();
        $this->assertEquals(self::ALPHA, $priorityList->current());
        $priorityList->add(self::BRAVO, 20);
        $this->assertEquals(self::BRAVO, $priorityList->current());
        $priorityList->add(self::BRAVO, 19);
        $this->assertEquals(19, $priorityList->currentPriority());
        return $priorityList;
    }

    /**
     * @expectedException \Exception
     */
    public function testObjectSafety()
    {
        $priorityList = new PriorityList(\stdClass::class);
        $priorityList->add(new PriorityList());
    }

}
