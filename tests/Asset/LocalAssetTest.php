<?php

namespace PETest\Component\Asset\Asset;

use PE\Component\Asset\Asset\LocalAsset;
use PE\Component\Asset\Exception\InvalidArgumentException;
use PE\Component\Asset\Exception\RuntimeException;

class LocalAssetTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructWithURIWithHostThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);
        new LocalAsset('https://example.com');
    }

    public function testConstructWithURIWithoutHostNotThrowsException()
    {
        new LocalAsset('/foo');
    }

    public function testGetLastModifiedThrowsExceptionIfRootDIRNotSet()
    {
        $this->expectException(RuntimeException::class);
        (new LocalAsset('/local.css'))->getLastModified();
    }

    public function testGetLastModifiedShouldReturnInteger()
    {
        $asset = new LocalAsset('/local.css');
        $asset->setRootDIR(__DIR__ . '/../TestAsset');

        static::assertSame(filemtime(__DIR__ . '/../TestAsset/local.css'), $asset->getLastModified());
    }

    public function testGetContentThrowsExceptionIfRootDirNotSet()
    {
        $this->expectException(RuntimeException::class);
        (new LocalAsset('/local.css'))->getContent();
    }

    public function testGetContent()
    {
        $asset = new LocalAsset('/local.css');
        $asset->setRootDIR(__DIR__ . '/../TestAsset');

        static::assertSame(file_get_contents(__DIR__ . '/../TestAsset/local.css'), $asset->getContent());
    }

    public function testGetHash()
    {
        $asset = new LocalAsset($uri = '/foo');
        $hash  = md5(LocalAsset::class . $uri);

        static::assertSame($hash, $asset->getHash());
    }
}
