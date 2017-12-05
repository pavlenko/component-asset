<?php

namespace PETest\Component\Asset\Renderer;

use PE\Component\Asset\Asset\AssetCollection;
use PE\Component\Asset\Asset\InlineAsset;
use PE\Component\Asset\Asset\LocalAsset;
use PE\Component\Asset\Renderer\CSSRenderer;

class CSSRendererTest extends \PHPUnit_Framework_TestCase
{
    public function testRenderInlineAsset()
    {
        $collection = new AssetCollection();
        $collection->add('inline', new InlineAsset('body { padding: 0}'));
        $collection->enqueue('inline');

        $renderer = new CSSRenderer();

        static::assertSame('<style>body { padding: 0}</style>', $renderer->render($collection));
    }

    public function testRenderInlineAssetWithMedia()
    {
        $collection = new AssetCollection();
        $collection->add('inline', new InlineAsset('body { padding: 0}', [], ['media' => 'screen']));
        $collection->enqueue('inline');

        $renderer = new CSSRenderer();

        static::assertSame('<style media="screen">body { padding: 0}</style>', $renderer->render($collection));
    }

    public function testRenderURIAsset()
    {
        $collection = new AssetCollection();
        $collection->add('uri', new LocalAsset('/foo.js'));
        $collection->enqueue('uri');

        $renderer = new CSSRenderer();

        static::assertSame('<link rel="stylesheet" href="/foo.js">', $renderer->render($collection));
    }

    public function testRenderURIAssetWithMedia()
    {
        $collection = new AssetCollection();
        $collection->add('uri', new LocalAsset('/foo.js', [], ['media' => 'screen']));
        $collection->enqueue('uri');

        $renderer = new CSSRenderer();

        static::assertSame('<link media="screen" rel="stylesheet" href="/foo.js">', $renderer->render($collection));
    }
}
