<?php

namespace PETest\Component\Asset\Asset;

use PE\Component\Asset\Asset\AbstractAsset;

class AbstractAssetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param array $dependencies
     * @param array $extras
     *
     * @return AbstractAsset|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createAsset(array $dependencies = [], array $extras = [])
    {
        /* @var $asset AbstractAsset|\PHPUnit_Framework_MockObject_MockObject */
        $asset = $this->getMockBuilder(AbstractAsset::class)->getMockForAbstractClass();

        $reflection  = new \ReflectionClass(AbstractAsset::class);
        $constructor = $reflection->getConstructor();
        $constructor->invoke($asset, $dependencies, $extras);

        return $asset;
    }

    public function testConstruct()
    {
        $asset = $this->createAsset(['foo']);

        static::assertSame(['foo'], $asset->getDependencies());
    }

    public function testGetExtras()
    {
        static::assertSame(['foo' => 'bar'], $this->createAsset([], ['foo' => 'bar'])->getExtras());
    }

    public function testGetKnownExtraShouldReturnValue()
    {
        static::assertSame('bar', $this->createAsset([], ['foo' => 'bar'])->getExtra('foo'));
    }

    public function testGetUnknownExtraShouldReturnNull()
    {
        static::assertNull($this->createAsset()->getExtra('unknown'));
    }

    public function testGetUnknownExtraShouldReturnDefault()
    {
        static::assertSame('default', $this->createAsset()->getExtra('unknown', 'default'));
    }

    public function testRootURI()
    {
        $asset = $this->createAsset();

        static::assertNull($asset->getRootURI());

        $asset->setRootURI('foo');

        static::assertSame('foo', $asset->getRootURI());
    }

    public function testRootDIR()
    {
        $asset = $this->createAsset();

        static::assertNull($asset->getRootDIR());

        $asset->setRootDIR('foo');

        static::assertSame('foo', $asset->getRootDIR());
    }

    public function testContent()
    {
        $asset = $this->createAsset();

        static::assertNull($asset->getContent());

        $asset->setContent('foo');

        static::assertSame('foo', $asset->getContent());
    }

    public function testURI()
    {
        $asset = $this->createAsset();

        static::assertNull($asset->getURI());

        $asset->setURI('foo');

        static::assertSame('foo', $asset->getURI());
    }
}
