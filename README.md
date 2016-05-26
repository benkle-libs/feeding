A rule-based RSS and Atom parser
================================

This PHP library contains an extensible, rule based parser for RSS and Atom news feeds.
It's way of working is inpsired by [Alex Debril's feed-io](https://packagist.org/packages/debril/feed-io), but it's been
rewritten from scratch to be a bit cleaner and more easily extensible. It doesn't have all the features of the original,
but you can easily create new parsing standards or extend existing one without rummaging through the library code.

Requirements
------------

 * PHP 5.6+
 * [guzzlehttp/guzzle](https://packagist.org/packages/guzzlehttp/guzzle) 6.2+
 * [masterminds/html5](https://packagist.org/packages/masterminds/html5) 2.2+
 * [psr/log](https://packagist.org/packages/psr/log) 1.0+

Installation
------------

_Feeding_ can be included in the usual way with composer:

```sh
    composer require benkle/feeding
```

Usage
-----

For a quick start you can instantiate the class `\Benkle\Feeding\Reader` and directly fetch a feed:

```php
$reader = new \Benkle\Feeding\Reader();
$feed = $reader->read('http://xkcd.com/atom.xml');

echo $feed->getTitle() . PHP_EOL;
foreach ($feed->getItems() as $item) {
    echo "\t" . $item->getTitle() . PHP_EOL;
}
```

The `Reader::parse` can take urls, file pathes or the direct feed source, and will select one of the preexisting feed standards to parse the data into objects.

### Create your own rules

_Feeding_ is based on rules, which are organized on standards.

A rule can be any class implementing `\Benkle\Feeding\Interfaces\RuleInterface`:

```php
class MyRule implements \Benkle\Feeding\Interfaces\RuleInterface {
    public function canHandle(\DOMNode $node, \Benkle\Feeding\Interfaces\NodeInterface $target)
    {
        return $node->nodeName == 'title' && $target instanceof \Benkle\Feeding\Interfaces\ChannelInterface;
    }

    public function handle(\Benkle\Feeding\Parser $parser, \DOMNode $node, \Benkle\Feeding\Interfaces\NodeInterface $target)
    {
        /** \Benkle\Feeding\Interfaces\ChannelInterface $target */
        $target->setTitle($node->nodeValue);
    }
}
```

Rules can be added to any standard via it's rule set, which is a priority ordered list:

```php
/** @var \Benkle\Feeding\Interfaces\StandardInterface $standard */
foreach ($reader->getStandards() as $standard) {
    $standard->getRules()->add(new MyRule(), 5); // Rules are ordered by priority
}
```

But often you might want to aggregate all your rules in a standard. Standards are classes implementing `\Benkle\Feeding\Interfaces\StandardInterface`:

```php
class MyStandard implements \Benkle\Feeding\Interfaces\StandardInterface {
    use \Benkle\Feeding\Traits\WithParserTrait;
    use \Benkle\Feeding\Traits\WithRuleSetTrait;

    public function __construct()
    {
        $this->getRules()->add(new MyRule(), 5);
    }

    public function newFeed()
    {
        return new \Benkle\Feeding\Feed();
    }

    public function getRootNode(\DOMDocument $dom)
    {
        return $dom->getElementsByTagName('feed')->item(0);
    }


    public function canHandle(\DOMDocument $dom)
    {
        return $dom->getElementsByTagName('feed')->length > 0;
    }
}
```

Adding a standard to a Reader is just as simple as adding a rule to a standard:

```php
$reader->getStandards()->add(new MyStandard(), 5);
```

Included standards are:

 * `Atom10Standard` for Atom 1.0
 * `RSS09Standard` for RSS 0.9
 * `RSS10Standard` for RSS 1.0
 * `RSS20Standard` for RSS 2.0

### Set your own DOM parser

This library uses the PHP DOM library classes for it's XML traversing. To use your own library you have to write a wrapper arround it implementing `\Benkle\Feeding\Interfaces\DOMParserInterface`:

```php
$reader->setDomParser(new class implements \Benkle\Feeding\Interfaces\DOMParserInterface {
    public function parse($source)
    {
        $parser = new \MyFancy\DomParser();
        return $parser->parse($source);
    }
});
```

_Feeding_ already include a wrapper for the standard library, which is fast but fails when a feeds isn't valid XML, and for the [Masterminds HTML5 parser](https://packagist.org/packages/masterminds/html5), which is more fault tolerant, but also way slower. It also includes a meta wrapper which will try any number of other wrappers it is given, allowing you to balance speed and reliability:

```php
$reader->setDomParser(
    new \Benkle\Feeding\DOMParsers\FallbackStackParser(
        new \Benkle\Feeding\DOMParsers\PHPDOMParser(),
        new \Benkle\Feeding\DOMParsers\MastermindsHTML5Parser()
    )
);
```

__Note:__ This Wrapper implements `Psr\Log\LoggerAwareInterface` and will write notices whenever any of it's protegé throws an exception!

### Set your own file access

If you need your own access (e.g. because you want to use [flysystem](http://flysystem.thephpleague.com/)) you have to write a wrapper as well, this time implementing `\Benkle\Feeding\Interfaces\FileAccessInterface`:

```php
$reader->setFileAccess(new class implements \Benkle\Feeding\Interfaces\FileAccessInterface {
    private $myFs;

    public function __construct()
    {
        $this->myFs = new MyFs();
    }

    public function exists($filename)
    {
        return $this->myFs->exists($filename);
    }

    public function get($filename)
    {
        return $this->myFs->open($filename);
    }
});
```

### More control

If you need more control over what standards are loaded, and don't need file and http access, you can use the `\Benkle\Feeding\BareReader` class:

```php
$reader = new \Benkle\Feeding\BareReader();
$reader->setDomParser(new \Benkle\Feeding\DOMParsers\PHPDOMParser());
$reader->getStandards()->add(new \Benkle\Feeding\Standards\RSS\RSS20Standard());
```

TODO
----

 * Add support for enclosures and relational links.
 * Moved included class `PriorityList` to a utility package.
 * Put Guzzle behind a facade similar to the `FileAccessInterface`?
