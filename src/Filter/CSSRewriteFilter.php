<?php

namespace PE\Component\Asset\Filter;

use PE\Component\Asset\Asset\AssetInterface;

/**
 * Process rewrite asset source urls
 */
class CSSRewriteFilter implements FilterInterface
{
    const REGEX_URLS            = '/url\((["\']?)(?P<url>.*?)(\\1)\)/';
    const REGEX_IMPORTS         = '/@import (?:url\()?(\'|"|)(?P<url>[^\'"\)\n\r]*)\1\)?;?/';
    const REGEX_IMPORTS_NO_URLS = '/@import (?!url\()(\'|"|)(?P<url>[^\'"\)\n\r]*)\1;?/';
    const REGEX_IE_FILTERS      = '/src=(["\']?)(?P<url>.*?)\\1/';

    /**
     * @inheritDoc
     */
    public function filter(AssetInterface $source, AssetInterface $target)
    {
        $content = $source->getContent();

        $scheme = parse_url($target->getRootURI(), PHP_URL_SCHEME);
        $host   = ($scheme ?: 'http') . '://' . parse_url($target->getRootURI(), PHP_URL_HOST);

        $callback = function($matches) use ($source, $host) {
            if (false !== strpos($matches['url'], '://') || 0 === strpos($matches['url'], '//') || 0 === strpos($matches['url'], 'data:')) {
                // absolute or protocol-relative or data uri
                return $matches[0];
            }

            if (isset($matches['url'][0]) && '/' === $matches['url'][0]) {
                // root relative
                return str_replace($matches['url'], $host . $matches['url'], $matches[0]);
            }

            return str_replace($matches['url'], $source->getRootURI() . '/' . $matches['url'], $matches[0]);
        };

        $content = preg_replace_callback(static::REGEX_URLS, $callback, $content);
        $content = preg_replace_callback(static::REGEX_IMPORTS_NO_URLS, $callback, $content);
        $content = preg_replace_callback(static::REGEX_IE_FILTERS, $callback, $content);

        $source->setContent($content);
    }
}