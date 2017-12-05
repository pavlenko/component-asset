<?php

namespace PE\Component\Asset\Filter;

use PE\Component\Asset\Asset\AssetInterface;

interface FilterInterface
{
    /**
     * Filters an asset
     *
     * @param AssetInterface $source
     * @param AssetInterface $target
     */
    public function filter(AssetInterface $source, AssetInterface $target);
}