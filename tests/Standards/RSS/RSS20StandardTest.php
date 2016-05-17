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

namespace Benkle\FeedParser\Standards\RSS;


use Benkle\FeedParser\Interfaces\StandardInterface;

class RSS20StandardTest extends \PHPUnit_Framework_TestCase
{

    public function testNewStandard()
    {
        $standard = new RSS20Standard();
        $this->assertInstanceOf(StandardInterface::class, $standard);
    }

    public function testCanHandleDOMWithProperVersion()
    {
        $standard = new RSS20Standard();
        $dom = new \DOMDocument();
        $root = $dom->createElement('rss');
        $version = $dom->createAttribute('version');
        $root->appendChild($version);
        $dom->appendChild($root);

        $versionMap = [
            '0.9'   => false,
            '0.8'   => false,
            '0.80'  => false,
            '0.90'  => false,
            '0.91'  => false,
            '0.92'  => false,
            '0.99'  => false,
            '1.0'   => false,
            '2.0'   => true,
            '2.0.1' => true,
            '2.01'  => true,
            '2.1'   => false,
        ];

        foreach ($versionMap as $versionNr => $canHandle) {
            $version->nodeValue = $versionNr;
            $this->assertEquals($canHandle, $standard->canHandle($dom));
        }
    }

}
