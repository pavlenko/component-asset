<?php

namespace PETest\Component\Asset\Renderer;

use PE\Component\Asset\Asset\AssetCollection;
use PE\Component\Asset\Asset\InlineAsset;
use PE\Component\Asset\Asset\LocalAsset;
use PE\Component\Asset\Renderer\JSRenderer;

class JSRendererTest extends \PHPUnit_Framework_TestCase
{
    public function testRenderInlineAsset()
    {
        $collection = new AssetCollection();
        $collection->add('inline', new InlineAsset('let i = 0'));
        $collection->enqueue('inline');

        $renderer = new JSRenderer();

        static::assertSame('<script>let i = 0</script>', $renderer->render($collection));
    }

    public function testRenderInlineAssetWithType()
    {
        $collection = new AssetCollection();
        $collection->add('inline', new InlineAsset('<% var i = 0 %>', [], ['type' => 'text/html']));
        $collection->enqueue('inline');

        $renderer = new JSRenderer();

        static::assertSame('<script type="text/html"><% var i = 0 %></script>', $renderer->render($collection));
    }

    public function testRenderURIAsset()
    {
        $collection = new AssetCollection();
        $collection->add('uri', new LocalAsset('/foo.js'));
        $collection->enqueue('uri');

        $renderer = new JSRenderer();

        static::assertSame('<script src="/foo.js"></script>', $renderer->render($collection));
    }

    public function testRenderURIAssetWithAttributes()
    {
        $collection = new AssetCollection();
        $collection->add('uri', new LocalAsset('/foo.js', [], ['async' => true, 'defer' => true]));
        $collection->enqueue('uri');

        $renderer = new JSRenderer();

        static::assertSame(
            '<script src="/foo.js" async="async" defer="defer"></script>',
            $renderer->render($collection)
        );
    }
}
