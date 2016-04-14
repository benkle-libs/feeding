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

namespace Benkle\FeedParser;


use Benkle\FeedParser\Interfaces\RuleInterface;

/**
 * Class RuleSet
 * Manages a prioritzed set of parser rules.
 * @package Benkle\FeedParser
 */
class RuleSet
{
    /** @var RuleInterface[] */
    private $rules = [];

    /** @var bool */
    private $sorted = false;

    /**
     * Add a rule to the set.
     * @param RuleInterface $rule
     * @param int $priority The priority of the rule. Lowest first. Default is 50.
     * @return $this
     */
    public function addRule(RuleInterface $rule, $priority = 50) {
        $this->removeRule($rule);
        $this->rules[] = ['rule' => $rule, 'priority' => $priority];
        $this->sorted = false;
        return $this;
    }

    /**
     * Remove a rule from the set.
     * @param RuleInterface $rule
     * @return $this
     */
    public function removeRule(RuleInterface $rule)
    {
        foreach ($this->rules as $i => $rule) {
            if ($rule['rule'] == $rule) {
                unset($this->rules[$i]);
            }
        }
        $this->rules = array_values($this->rules);
        return $this;
    }

    /**
     * Get the list of rules, sorted by priority.
     * @return RuleInterface[]
     */
    public function getRules()
    {
        if (!$this->sorted) {
            // @see http://stackoverflow.com/a/9716530
            uasort($this->rules, function($a, $b) { return $a['priority'] - $b['priority']; });
            $this->sorted = true;
        }
        return array_values(array_map(function($rule) { return $rule['rule']; }, $this->rules));
    }
}
