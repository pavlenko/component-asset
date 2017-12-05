<?php

namespace PE\Component\Asset\Renderer;

use PE\Component\Asset\Asset\AssetCollection;
use PE\Component\Asset\Asset\InlineAsset;
use PE\Component\Asset\Asset\LocalAsset;
use PE\Component\Asset\Asset\RemoteAsset;

class CSSRenderer extends AbstractRenderer
{
    /**
     * @inheritdoc
     */
    public function render(AssetCollection $collection)
    {
        $result = '';

        foreach ($collection->enqueued() as $asset) {
            $attributes = [];

            if ($media = $asset->getExtra('media')) {
                $attributes['media'] = $media;
            }

            if ($asset instanceof InlineAsset) {
                $result .= sprintf(
                    '<style%s>%s</style>',
                    ($attributes = $this->renderAttributes($attributes)) ? ' ' . $attributes: '',
                    $asset->getContent()
                );
            } else if ($asset instanceof LocalAsset || $asset instanceof RemoteAsset) {
                $attributes['rel']  = 'stylesheet';
                $attributes['href'] = $asset->getURI();

                $result .= sprintf('<link %s>', $this->renderAttributes($attributes));
            }
        }

        return $result;
    }
}