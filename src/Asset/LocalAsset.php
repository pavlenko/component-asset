<?php

namespace PE\Component\Asset\Asset;

use PE\Component\Asset\Exception\ExceptionInterface;
use PE\Component\Asset\Exception\InvalidArgumentException;
use PE\Component\Asset\Exception\RuntimeException;

/**
 * Represents asset from local file
 */
class LocalAsset extends AbstractAsset
{
    /**
     * Constructor
     *
     * @param string   $uri         Local file uri
     * @param string[] $dependencies Asset dependencies
     * @param array    $extras
     *
     * @throws ExceptionInterface If uri does not contain host
     */
    public function __construct($uri, array $dependencies = [], array $extras = [])
    {
        if (parse_url($uri, PHP_URL_HOST)) {
            throw new InvalidArgumentException('Local asset uri must not contain host');
        }

        $this->setURI($uri);
        parent::__construct($dependencies, $extras);
    }

    /**
     * @inheritdoc
     *
     * @throws ExceptionInterface If source dir not set or file not exists
     */
    public function getContent()
    {
        if ($content = parent::getContent()) {
            return $content;
        }

        $path = $this->getRootDIR() . $this->getURI();

        if (!$this->getRootDIR() || !is_file($path)) {
            throw new RuntimeException(sprintf('The local file "%s" does not exist.', $path));
        }

        $this->setContent(@file_get_contents($path));
        return parent::getContent();
    }

    /**
     * @inheritDoc
     *
     * @throws ExceptionInterface If source dir not set or file not exists
     */
    public function getLastModified()
    {
        $path = $this->getRootDIR() . $this->getURI();

        if (!$this->getRootDIR() || !is_file($path)) {
            throw new RuntimeException(sprintf('The local file "%s" does not exist.', $path));
        }

        return filemtime($path);
    }

    /**
     * @inheritDoc
     */
    public function getHash()
    {
        return md5(static::class . $this->getURI());
    }
}