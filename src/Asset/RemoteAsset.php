<?php

namespace PE\Component\Asset\Asset;

use PE\Component\Asset\Exception\ExceptionInterface;
use PE\Component\Asset\Exception\InvalidArgumentException;

/**
 * Represents asset from remote file
 */
class RemoteAsset extends AbstractAsset
{
    /**
     * Constructor
     *
     * @param string   $uri          Remote file uri, must starts from "//" or "http(s)://"
     * @param string[] $dependencies Asset dependencies
     * @param array    $extras
     *
     * @throws ExceptionInterface If uri does not contain host
     */
    public function __construct($uri, array $dependencies = [], array $extras = [])
    {
        if (!parse_url($uri, PHP_URL_HOST)) {
            throw new InvalidArgumentException('Remote asset uri must contain host');
        }

        $this->setURI($uri);
        parent::__construct($dependencies, $extras);
    }

    /**
     * @inheritDoc
     */
    public function getContent()
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function setContent($content)
    {
        // Do nothing
    }

    /**
     * @inheritDoc
     */
    public function getLastModified()
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getHash()
    {
        return md5(static::class . $this->getURI());
    }
}