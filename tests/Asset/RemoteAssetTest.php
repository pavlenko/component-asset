<?php

namespace PETest\Component\Asset\Asset;

use PE\Component\Asset\Asset\RemoteAsset;
use PE\Component\Asset\Exception\InvalidArgumentException;

class RemoteAssetTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructWithURIWithoutHostThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);
        new RemoteAsset('/foo');
    }

    public function testConstructWithURIWithHostNotThrowsException()
    {
        new RemoteAsset('//example.com/foo');
        new RemoteAsset('https://example.com/foo');
    }

    public function testGetLastModifiedShouldReturnNull()
    {
        static::assertNull((new RemoteAsset('//example.com/foo'))->getLastModified());
    }

    public function testGetContentShouldReturnNull()
    {
        static::assertNull((new RemoteAsset($uri = '//example.com/foo'))->getContent());
    }

    public function testSetContentShouldNotModify()
    {
        $asset = new RemoteAsset($uri = '//example.com/foo');
        $asset->setContent('foo');

        static::assertNull($asset->getContent());
    }

    public function testGetHash()
    {
        $asset = new RemoteAsset($uri = '//example.com/foo');
        $hash  = md5(RemoteAsset::class . $uri);

        static::assertSame($hash, $asset->getHash());
    }
}
