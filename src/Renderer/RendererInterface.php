<?php

namespace PE\Component\Asset\Renderer;

use PE\Component\Asset\Asset\AssetCollection;
use PE\Component\Asset\Exception\ExceptionInterface;

interface RendererInterface
{
    /**
     * Render asset collection to string
     *
     * @param AssetCollection $collection
     *
     * @return string
     *
     * @throws ExceptionInterface
     */
    public function render(AssetCollection $collection);
}