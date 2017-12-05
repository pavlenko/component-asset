<?php

namespace PE\Component\Asset\Renderer;

use PE\Component\Asset\Asset\AssetCollection;
use PE\Component\Asset\Asset\InlineAsset;
use PE\Component\Asset\Asset\LocalAsset;
use PE\Component\Asset\Asset\RemoteAsset;

class JSRenderer extends AbstractRenderer
{
    /**
     * @inheritdoc
     */
    public function render(AssetCollection $collection)
    {
        $result = '';

        foreach ($collection->enqueued() as $asset) {
            $attributes = [];

            if ($type = $asset->getExtra('type')) {
                $attributes['type'] = $type;
            }

            if ($asset instanceof InlineAsset) {
                $result .= sprintf(
                    '<script%s>%s</script>',
                    ($attributes = $this->renderAttributes($attributes)) ? ' ' . $attributes : '',
                    $asset->getContent()
                );
            } else if ($asset instanceof LocalAsset || $asset instanceof RemoteAsset) {
                $attributes['src'] = $asset->getURI();

                if ($asset->getExtra('async')) {
                    $attributes['async'] = true;
                }

                if ($asset->getExtra('defer')) {
                    $attributes['defer'] = true;
                }

                $result .= sprintf('<script %s></script>', $this->renderAttributes($attributes));
            }
        }

        return $result;
    }
}