<?php

namespace PETest\Component\Asset\Renderer;

use PE\Component\Asset\Asset\AssetCollection;
use PE\Component\Asset\Asset\InlineAsset;
use PE\Component\Asset\Asset\LocalAsset;
use PE\Component\Asset\Asset\RemoteAsset;
use PE\Component\Asset\Filter\FilterInterface;
use PE\Component\Asset\Renderer\ConcatenateRenderer;
use PE\Component\Asset\Renderer\JSRenderer;

class ConcatenateRendererTest extends \PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        @unlink(__DIR__ . '/../TestAsset/concatenated.js');
    }

    public function testConcatenate()
    {
        $collection = new AssetCollection();

        $collection->add('remote', new RemoteAsset('//example.com/foo'));
        $collection->add('local', new LocalAsset('/local.js'));
        $collection->add('inline', new InlineAsset('let j = 0;'));

        $collection->enqueue('remote');
        $collection->enqueue('local');
        $collection->enqueue('inline');

        touch(__DIR__ . '/../TestAsset/local.js');

        $renderer = new ConcatenateRenderer(new JSRenderer(), '/concatenated.js', __DIR__ . '/../TestAsset', '', 10000);

        $filter = $this->createMock(FilterInterface::class);
        $filter->expects(static::atLeastOnce())->method('filter');

        $renderer->setFilters([$filter]);

        static::assertSame('<script src="/concatenated.js"></script>', $renderer->render($collection));
        sleep(1);
        static::assertSame('<script src="/concatenated.js"></script>', $renderer->render($collection));
    }
}
