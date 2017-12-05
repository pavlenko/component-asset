<?php

namespace PETest\Component\Asset;

use PE\Component\Asset\Asset\AssetCollection;
use PE\Component\Asset\AssetManager;
use PE\Component\Asset\Exception\InvalidArgumentException;
use PE\Component\Asset\Exception\UnexpectedValueException;
use PE\Component\Asset\Renderer\RendererInterface;

class AssetManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetThrowsExceptionIfNameInvalid()
    {
        $this->expectException(UnexpectedValueException::class);
        (new AssetManager())->get(false);
    }

    public function testSetThrowsExceptionIfNameInvalid()
    {
        $this->expectException(UnexpectedValueException::class);
        (new AssetManager())->set(false, new AssetCollection());
    }

    public function testGetThrowsExceptionIfAutoCreateDisabled()
    {
        $this->expectException(InvalidArgumentException::class);
        (new AssetManager([], [], false))->get('undefined');
    }

    public function testGetNotThrowsExceptionIfAutoCreateEnabled()
    {
        static::assertInstanceOf(AssetCollection::class, (new AssetManager())->get('undefined'));
    }

    public function testGetCollectionIsSameAfterSet()
    {
        $manager = new AssetManager();
        $manager->set('foo', $collection = new AssetCollection());

        static::assertSame($collection, $manager->get('foo'));
    }

    public function testRenderWithoutRendererReturnsEmptyString()
    {
        static::assertSame('', (new AssetManager())->render('foo'));
    }

    public function testRenderWithRendererReturnsItResult()
    {
        $renderer = $this->createMock(RendererInterface::class);
        $renderer
            ->expects(static::once())
            ->method('render')
            ->willReturn('foo-result');

        static::assertSame('foo-result', (new AssetManager([], ['foo' => $renderer]))->render('foo'));
    }
}
