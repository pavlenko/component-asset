<?php

namespace PETest\Component\Asset\Asset;

use PE\Component\Asset\Asset\InlineAsset;

class InlineAssetTest extends \PHPUnit_Framework_TestCase
{
    public function testGetLastModifiedShouldReturnNullIfNotSet()
    {
        static::assertNull((new InlineAsset('//example.com/foo'))->getLastModified());
    }

    public function testGetLastModifiedShouldReturnIntegerIfSet()
    {
        $asset = new InlineAsset('//example.com/foo');
        $asset->setLastModified($time = time());

        static::assertSame($time, $asset->getLastModified());
    }

    public function testGetContentShouldReturnConstructorArgument()
    {
        $asset = new InlineAsset($content = '//example.com/foo');
        static::assertSame($content, $asset->getContent());
    }

    public function testGetHash()
    {
        $asset = new InlineAsset($content = '//example.com/foo');
        $hash  = md5(InlineAsset::class . $content);

        static::assertSame($hash, $asset->getHash());
    }
}
