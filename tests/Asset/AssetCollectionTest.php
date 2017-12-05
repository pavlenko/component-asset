<?php

namespace PETest\Component\Asset\Asset;

use PE\Component\Asset\Asset\AssetCollection;
use PE\Component\Asset\Asset\AssetInterface;
use PE\Component\Asset\Asset\InlineAsset;
use PE\Component\Asset\Asset\LocalAsset;
use PE\Component\Asset\Asset\RemoteAsset;
use PE\Component\Asset\Exception\RuntimeException;
use PE\Component\Asset\Exception\UnexpectedValueException;

class AssetCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testAddThrowsExceptionIfNameInvalid()
    {
        /* @var $asset AssetInterface|\PHPUnit_Framework_MockObject_MockObject */
        $asset = $this->createMock(AssetInterface::class);

        $this->expectException(UnexpectedValueException::class);

        $collection = new AssetCollection();
        $collection->add(false, $asset);
    }

    public function testAddNotThrowsExceptionIfAssetValid()
    {
        /* @var $asset AssetInterface|\PHPUnit_Framework_MockObject_MockObject */
        $asset = $this->createMock(AssetInterface::class);

        $collection = new AssetCollection();
        $collection->add('foo', $asset);
    }

    public function testPreventMultipleAddWithSameName()
    {
        /* @var $asset1 AssetInterface|\PHPUnit_Framework_MockObject_MockObject */
        $asset1 = $this->createMock(AssetInterface::class);

        /* @var $asset2 AssetInterface|\PHPUnit_Framework_MockObject_MockObject */
        $asset2 = $this->createMock(AssetInterface::class);

        $collection = new AssetCollection();

        $collection->add('foo', $asset1);
        $collection->add('foo', $asset2);

        static::assertSame($asset1, $collection->get('foo'));
    }

    public function testAllAddRemove()
    {
        /* @var $asset AssetInterface|\PHPUnit_Framework_MockObject_MockObject */
        $asset = $this->createMock(AssetInterface::class);

        $collection = new AssetCollection();

        static::assertCount(0, $collection->all());

        $collection->add('foo', $asset);

        static::assertCount(1, $collection->all());

        $collection->remove('foo');

        static::assertCount(0, $collection->all());
    }

    public function testEnqueued()
    {
        /* @var $asset1 AssetInterface|\PHPUnit_Framework_MockObject_MockObject */
        $asset1 = $this->createMock(AssetInterface::class);
        $asset1->method('getDependencies')->willReturn(['bar', 'baz']);

        /* @var $asset2 AssetInterface|\PHPUnit_Framework_MockObject_MockObject */
        $asset2 = $this->createMock(AssetInterface::class);
        $asset2->method('getDependencies')->willReturn([]);

        /* @var $asset3 AssetInterface|\PHPUnit_Framework_MockObject_MockObject */
        $asset3 = $this->createMock(AssetInterface::class);
        $asset3->method('getDependencies')->willReturn(['bar']);

        $collection = new AssetCollection();

        $collection->add('foo', $asset1);
        $collection->add('bar', $asset2);
        $collection->add('baz', $asset3);

        $collection->enqueue('foo');

        static::assertEquals(['bar', 'baz', 'foo'], array_keys($collection->enqueued()));
    }

    public function testNotFoundDependencyNotThrowsException()
    {
        /* @var $asset AssetInterface|\PHPUnit_Framework_MockObject_MockObject */
        $asset = $this->createMock(AssetInterface::class);
        $asset->method('getDependencies')->willReturn(['bar']);

        $collection = new AssetCollection();

        $collection->add('foo', $asset);

        $collection->enqueue('foo');
        $collection->enqueued();
    }

    public function testCircularDependencyThrowsException()
    {
        /* @var $asset1 AssetInterface|\PHPUnit_Framework_MockObject_MockObject */
        $asset1 = $this->createMock(AssetInterface::class);
        $asset1->method('getDependencies')->willReturn(['bar']);

        /* @var $asset2 AssetInterface|\PHPUnit_Framework_MockObject_MockObject */
        $asset2 = $this->createMock(AssetInterface::class);
        $asset2->method('getDependencies')->willReturn(['foo']);

        $this->expectException(RuntimeException::class);

        $collection = new AssetCollection();

        $collection->add('foo', $asset1);
        $collection->add('bar', $asset2);

        $collection->enqueue('foo');
        $collection->enqueued();
    }

    public function testEnqueuedEmptyIfRemoved()
    {
        /* @var $asset AssetInterface|\PHPUnit_Framework_MockObject_MockObject */
        $asset = $this->createMock(AssetInterface::class);
        $asset->method('getDependencies')->willReturn([]);

        $collection = new AssetCollection();

        $collection->add('foo', $asset);

        $collection->enqueue('foo');
        self::assertEquals(['foo'], array_keys($collection->enqueued()));

        $collection->dequeue('foo');
        self::assertEquals([], array_keys($collection->enqueued()));
    }

    public function testGetLastModifiedShouldReturnNullIfCollectionEmpty()
    {
        static::assertNull((new AssetCollection())->getLastModified());
    }

    public function testGetLastModifiedShouldReturnZeroIfCollectionHasOnlyRemoteAssets()
    {
        $collection = new AssetCollection();
        $collection->add('foo', new RemoteAsset('//foo.com'));
        $collection->add('bar', new RemoteAsset('//bar.com'));

        static::assertSame(0, $collection->getLastModified());
    }

    public function testGetLastModifiedShouldReturnHighestIntegerIfCollectionHasLocalOrInlineAssets()
    {
        $collection = new AssetCollection();

        $asset1 = new LocalAsset('/local.css');
        $asset1->setRootDIR(__DIR__ . '/../TestAsset');

        $asset2 = new InlineAsset('AAA');
        $asset2->setLastModified(time() - 10);

        $collection->add('foo', $asset1);
        $collection->add('bar', $asset2);

        static::assertSame(time() - 10, $collection->getLastModified());
    }
}
