<?php

namespace PE\Component\Asset\Asset;

use PE\Component\Asset\Exception\ExceptionInterface;
use PE\Component\Asset\Exception\RuntimeException;
use PE\Component\Asset\Exception\UnexpectedValueException;

class AssetCollection
{
    /**
     * @var AssetInterface[]
     */
    private $assets = [];

    /**
     * @var array
     */
    private $enqueued = [];

    /**
     * @var bool
     */
    private $sorted = false;

    /**
     * @return AssetInterface[]
     *
     * @throws ExceptionInterface
     */
    public function all()
    {
        $this->sort();
        return $this->assets;
    }

    /**
     * @return AssetInterface[]
     *
     * @throws ExceptionInterface
     */
    public function enqueued()
    {
        $this->sort();

        $enqueued = array_keys($this->enqueued);
        foreach ($this->assets as $asset) {
            foreach ($asset->getDependencies() as $dependency) {
                $enqueued[] = $dependency;
            }
        }

        return array_filter($this->assets, function($name) use ($enqueued) {
            return in_array($name, $enqueued, true);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * @param string         $name
     * @param AssetInterface $asset
     *
     * @return $this
     *
     * @throws ExceptionInterface
     */
    public function add($name, AssetInterface $asset)
    {
        UnexpectedValueException::validate($name, 'string');

        if (array_key_exists($name, $this->assets)) {
            // Prevent double add asset with same name
            return $this;
        }

        $this->sorted = false;
        $this->assets[$name] = $asset;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return AssetInterface|null
     */
    public function get($name)
    {
        return array_key_exists($name, $this->assets) ? $this->assets[$name] : null;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function remove($name)
    {
        $this->sorted = false;
        unset($this->assets[$name]);
        return $this;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function enqueue($name)
    {
        $this->enqueued[$name] = true;
        return $this;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function dequeue($name)
    {
        unset($this->enqueued[$name]);
        return $this;
    }

    /**
     * @return int|null
     */
    public function getLastModified()
    {
        if (!count($this->assets)) {
            return null;
        }

        $collectionLastModified = 0;

        foreach ($this->assets as $asset) {
            $assetLastModified = $asset->getLastModified();

            if ($assetLastModified > $collectionLastModified) {
                $collectionLastModified = $assetLastModified;
            }
        }

        return $collectionLastModified;
    }

    /**
     * Internal sort
     *
     * @throws ExceptionInterface
     */
    private function sort()
    {
        if ($this->sorted) {
            return;
        }

        $levels = [];
        foreach ($this->assets as $name => $asset) {
            $levels[] = $this->getDependencyLevel($name);
        }

        array_multisort($levels, SORT_ASC, $this->assets);

        $this->sorted = true;
    }

    /**
     * Calculate dependency level
     *
     * @param string $name
     * @param array  $passed
     *
     * @return int
     *
     * @throws ExceptionInterface
     */
    private function getDependencyLevel($name, array $passed = [])
    {
        if (!array_key_exists($name, $this->assets)) {
            return 0;
        }

        $dependencies = $this->assets[$name]->getDependencies();

        if (!count($dependencies)) {
            return 0;
        }

        if (in_array($name, $passed, true)) {
            throw new RuntimeException('Circular reference detected ' . implode(' -> ', $passed));
        }

        $passed[] = $name;

        $max = 0;

        foreach ($dependencies as $dependency) {
            $max = max($this->getDependencyLevel($dependency, $passed), $max);
        }

        return $max + 1;
    }
}