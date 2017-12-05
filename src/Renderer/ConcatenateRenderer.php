<?php

namespace PE\Component\Asset\Renderer;

use PE\Component\Asset\Asset\AssetCollection;
use PE\Component\Asset\Asset\LocalAsset;
use PE\Component\Asset\Asset\RemoteAsset;
use PE\Component\Asset\Exception\RuntimeException;
use PE\Component\Asset\Exception\UnexpectedValueException;
use PE\Component\Asset\Filter\FilterInterface;

class ConcatenateRenderer implements RendererInterface
{
    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var FilterInterface[]
     */
    private $filters = [];

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $rootDIR;

    /**
     * @var string
     */
    private $rootURI;

    /**
     * @var int
     */
    private $ttl;

    /**
     * ConcatenateRenderer constructor.
     *
     * @param RendererInterface $renderer
     * @param string            $path
     * @param string            $rootDIR
     * @param string            $rootURI
     * @param int               $ttl
     */
    public function __construct(RendererInterface $renderer, $path, $rootDIR, $rootURI, $ttl = 0)
    {
        $this->renderer = $renderer;
        $this->path     = $path;
        $this->rootDIR  = $rootDIR;
        $this->rootURI  = $rootURI;
        $this->ttl      = (int) $ttl;
    }

    /**
     * @param array $filters
     *
     * @throws UnexpectedValueException
     */
    public function setFilters(array $filters)
    {
        UnexpectedValueException::validate($filters, FilterInterface::class . '[]');
        $this->filters = $filters;
    }

    /**
     * @inheritDoc
     */
    public function render(AssetCollection $collection)
    {
        $path   = $this->rootDIR . $this->path;
        $hashes = [];

        $targetCollection = new AssetCollection();

        $targetAsset = new LocalAsset($this->path);
        $targetAsset->setRootDIR($this->rootDIR);
        $targetAsset->setRootURI($this->rootURI);

        // First add remote assets as is and get hashes of other assets
        foreach ($collection->enqueued() as $name => $asset) {
            // Set root dir for use in concatenation
            $asset->setRootDIR($this->rootDIR);
            $asset->setRootURI($this->rootURI);

            if ($asset instanceof RemoteAsset) {
                $targetCollection->add($name, $asset);
            } else {
                $hashes[] = $asset->getHash();
            }
        }

        // Check hashes count, if > 0 -> need processing
        if (count($hashes)) {
            $hash = md5(implode('', $hashes));

            // Check combined asset is fresh
            if (is_file($path) && time() < $this->ttl + $collection->getLastModified()) {
                $targetCollection->add($hash, $targetAsset);
                $targetCollection->enqueue($hash);
            } else {
                $content = [];

                foreach ($collection->enqueued() as $asset) {
                    foreach ($this->filters as $filter) {
                        $filter->filter($asset, $targetAsset);
                    }

                    $content[] = $asset->getContent();
                }

                if (count($content)) {
                    // @codeCoverageIgnoreStart
                    if (!@mkdir($dir = dirname($path), 0777, true) && !is_dir($dir)) {
                        throw new RuntimeException('Unable to create directory ' . $dir);
                    }

                    if (false === @file_put_contents($path, implode("\n", $content))) {
                        throw new RuntimeException('Unable to write file ' . $path);
                    }
                    // @codeCoverageIgnoreEnd

                    $targetCollection->add($hash, $targetAsset);
                    $targetCollection->enqueue($hash);
                }
            }
        }

        return $this->renderer->render($targetCollection);
    }
}