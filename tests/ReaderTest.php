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

namespace Benkle\Feeding;


use Benkle\Feeding\Interfaces\DOMParserInterface;
use Benkle\Feeding\Interfaces\FileAccessInterface;
use Benkle\Feeding\Interfaces\StandardInterface;
use GuzzleHttp\Client;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

class ReaderTest extends \PHPUnit_Framework_TestCase
{

    public function testNewReader()
    {
        $reader = new Reader();
        $this->assertInstanceOf(BareReader::class, $reader);
        $this->assertInstanceOf(Reader::class, $reader);
    }

    public function testSetAndGetHttpClient()
    {
        $reader = new Reader();
        $client = $this->getMock(Client::class);
        $this->assertEquals($reader, $reader->setHttpClient($client));
        $this->assertEquals($client, $reader->getHttpClient());
    }

    public function testSetAndGetFileAccess()
    {
        $reader = new Reader();
        $fileAccess = $this->getMock(FileAccessInterface::class);
        $this->assertEquals($reader, $reader->setFileAccess($fileAccess));
        $this->assertEquals($fileAccess, $reader->getFileAccess());
    }

    /**
     * @expectedException \Benkle\Feeding\Exceptions\FileNotFoundException
     * @expectedExceptionMessage File "test" not found
     */
    public function testReadFromNonexistingFile()
    {
        $reader = new Reader();
        $fileAccess = $this->getMock(FileAccessInterface::class);
        $fileAccess
            ->expects($this->exactly(1))
            ->method('exists')
            ->willReturn(false);
        $reader->setFileAccess($fileAccess);
        $reader->readFromFile('test');
    }

    public function testReadFromExistingFile()
    {
        $reader = new Reader();
        $this->mockBasicReader($reader);

        $fileAccess = $this->getMock(FileAccessInterface::class);
        $fileAccess
            ->expects($this->exactly(1))
            ->method('exists')
            ->with('/test.xml')
            ->willReturn(true);
        $fileAccess
            ->expects($this->exactly(1))
            ->method('get')
            ->with('/test.xml')
            ->willReturn('test');
        $reader->setFileAccess($fileAccess);

        $this->assertEquals('test', $reader->readFromFile('/test.xml'));
    }

    public function testReadFromRemote()
    {
        $reader = new Reader();
        $this->mockBasicReader($reader);

        $body = $this->getMock(StreamInterface::class);
        $body
            ->expects($this->exactly(1))
            ->method('getContents')
            ->willReturn('test');
        $response = $this->getMock(MessageInterface::class);
        $response
            ->expects($this->exactly(1))
            ->method('getBody')
            ->willReturn($body);
        $client = $this->getMock(Client::class, ['get']);
        $client
            ->expects($this->exactly(1))
            ->method('get')
            ->with('http://test')
            ->willReturn($response);
        $reader->setHttpClient($client);

        $this->assertEquals('test', $reader->readFromRemote('http://test'));
    }

    public function testReadRemote()
    {
        $reader = new Reader();
        $this->mockBasicReader($reader);

        $body = $this->getMock(StreamInterface::class);
        $body
            ->expects($this->exactly(1))
            ->method('getContents')
            ->willReturn('test');
        $response = $this->getMock(MessageInterface::class);
        $response
            ->expects($this->exactly(1))
            ->method('getBody')
            ->willReturn($body);
        $client = $this->getMock(Client::class, ['get']);
        $client
            ->expects($this->exactly(1))
            ->method('get')
            ->with('http://test')
            ->willReturn($response);
        $reader->setHttpClient($client);

        $this->assertEquals('test', $reader->read('http://test'));
    }

    public function testReadFile()
    {
        $reader = new Reader();
        $this->mockBasicReader($reader);

        $fileAccess = $this->getMock(FileAccessInterface::class);
        $fileAccess
            ->expects($this->atLeast(1))
            ->method('exists')
            ->with('/test.xml')
            ->willReturn(true);
        $fileAccess
            ->expects($this->exactly(1))
            ->method('get')
            ->with('/test.xml')
            ->willReturn('test');
        $reader->setFileAccess($fileAccess);

        $this->assertEquals('test', $reader->read('/test.xml'));
    }

    public function testReadSource()
    {
        $reader = new Reader();
        $this->mockBasicReader($reader);

        $fileAccess = $this->getMock(FileAccessInterface::class);
        $fileAccess
            ->expects($this->atLeast(1))
            ->method('exists')
            ->with('test')
            ->willReturn(false);
        $reader->setFileAccess($fileAccess);

        $this->assertEquals('test', $reader->read('test'));
    }

    private function mockBasicReader(BareReader $reader)
    {
        $parser = $this->getMock(DOMParserInterface::class);
        $parser
            ->expects($this->exactly(1))
            ->method('parse')
            ->with('test')
            ->willReturn(new \DOMDocument());
        $reader->setDomParser($parser);

        $standard = $this->getMock(StandardInterface::class);
        $standard
            ->expects($this->atLeast(1))
            ->method('canHandle')
            ->willReturn(false);
        $reader->getStandards()->add($standard, 10);

        $parser = $this
            ->getMockBuilder(Parser::class)
            ->disableOriginalConstructor()
            ->getMock();
        $parser
            ->expects($this->exactly(1))
            ->method('parse')
            ->willReturn('test');
        $standard = $this->getMock(StandardInterface::class);
        $standard
            ->expects($this->atLeast(1))
            ->method('canHandle')
            ->willReturn(true);
        $standard
            ->expects($this->exactly(1))
            ->method('getParser')
            ->willReturn($parser);
        $reader->getStandards()->add($standard, 10);
    }

}
