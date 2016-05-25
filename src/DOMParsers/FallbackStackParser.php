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

namespace Benkle\FeedParser\DOMParsers;


use Benkle\FeedParser\Interfaces\DOMParserInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * Class FallbackStackParser
 * Bundles other DOMParsers so you can try the fast and picky ones first and the slow and tolerant ones only when necessary.
 * @package Benkle\FeedParser\DOMParsers
 */
class FallbackStackParser implements DOMParserInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var DOMParserInterface[] */
    private $parsers = [];

    /**
     * FallbackStackParser constructor.
     * @param DOMParserInterface ...$parsers
     */
    public function __construct(DOMParserInterface ...$parsers)
    {
        $this->parsers = $parsers;
    }

    /**
     * Factory for cases where you don't know the exact set of parsers.
     * @param DOMParserInterface[] $parsers
     * @return FallbackStackParser
     */
    public static function create(array $parsers)
    {
        return new self(...$parsers);
    }


    /**
     * Parse a string into a DOMDocument.
     * @param $source
     * @return \DOMDocument
     */
    public function parse($source)
    {
        $result = false;
        foreach ($this->parsers as $parser) {
            try {
                $result = $parser->parse($source);
                if ($result instanceof \DOMDocument) {
                    break;
                }
            } catch (\Exception $e) {
                $this->logException($e);
            }
        }
        if (!($result instanceof \DOMDocument)) {
            throw new \DOMException('Unable to parse source');
        }
        return $result;
    }

    /**
     * Savely wrap the logger.
     * @param \Exception $e
     */
    private function logException(\Exception $e)
    {
        if (isset($this->logger)) {
            $this->logger->notice(
                $e->getMessage(), [
                                    'code' => $e->getCode(),
                                    'line' => $e->getLine(),
                                    'file' => $e->getFile(),
                                ]
            );
        } else {
            // @codeCoverageIgnoreStart
            error_log(sprintf('[%s] [%s:%d] %s', $e->getCode(), $e->getFile(), $e->getLine(), $e->getMessage()), E_NOTICE);
            // @codeCoverageIgnoreEnd
        }
    }
}
