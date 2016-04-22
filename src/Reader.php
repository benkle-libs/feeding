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


use Benkle\FeedParser\DOMParsers\FallbackStackParser;
use Benkle\FeedParser\DOMParsers\MastermindsHTML5Parser;
use Benkle\FeedParser\DOMParsers\PHPDOMParser;
use Benkle\FeedParser\FileAccess\BasicFileAccess;
use Benkle\FeedParser\Interfaces\FileAccessInterface;
use Benkle\FeedParser\Standards\Atom\Atom10Standard;
use Benkle\FeedParser\Standards\RSS\RSS09Standard;
use Benkle\FeedParser\Standards\RSS\RSS20Standard;
use GuzzleHttp\Client;

/**
 * Class Reader
 * This is the reader class for all those who only need the basic standards and just want to fetch a feed from the web
 * parse it.
 * @package Benkle\FeedParser
 */
class Reader extends BasicReader
{
    /** @var Client */
    private $httpClient;
    /** @var  FileAccessInterface */
    private $fileAccess;

    /**
     * Reader constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this
            ->setFileAccess(new BasicFileAccess())
            ->setHttpClient(new Client())
            ->setDomParser(
                new FallbackStackParser(
                    new PHPDOMParser(),
                    new MastermindsHTML5Parser()
                )
            )
            ->getStandards()
                ->add(new RSS09Standard())
                ->add(new RSS20Standard())
                ->add(new Atom10Standard())
        ;
    }

    /**
     * Get the http client.
     * @return Client
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * Set the http client.
     * @param Client $httpClient
     * @return $this
     */
    public function setHttpClient($httpClient)
    {
        $this->httpClient = $httpClient;
        return $this;
    }

    /**
     * Get the file access abstraction.
     * @return FileAccessInterface
     */
    public function getFileAccess()
    {
        return $this->fileAccess;
    }

    /**
     * Set the file access abstraction.
     * @param FileAccessInterface $fileAccess
     * @return $this
     */
    public function setFileAccess($fileAccess)
    {
        $this->fileAccess = $fileAccess;
        return $this;
    }

    /**
     * Read a feed from a file.
     * @param string $filename
     * @return Interfaces\FeedInterface
     * @throws \Exception
     */
    public function readFromFile($filename)
    {
        if (!$this->getFileAccess()->exists($filename)) {
            throw new \Exception(sprintf('File "%s" not found', $filename));
        }
        return parent::read($this->getFileAccess()->get($filename));
    }

    /**
     * Read a feed from a remote address.
     * @param string $url
     * @return Interfaces\FeedInterface
     * @throws \Exception
     */
    public function readFromRemote($url)
    {
        $source = $this->getHttpClient()->get($url)->getBody()->getContents();
        return parent::read($source);
    }

    /**
     * Read a feed from either a remote address, a file or the source.
     * @param string $feed
     * @return Interfaces\FeedInterface
     * @throws \Exception
     */
    public function read($feed)
    {
        if ((substr($feed, 0, 5) == 'http:') || (substr($feed, 0, 6) == 'https:')) {
            return $this->readFromRemote($feed);
        } elseif ($this->getFileAccess()->exists($feed)) {
            return $this->readFromFile($feed);
        } else {
            return parent::read($feed);
        }
    }
}
