<?php

namespace PE\Component\Asset\Asset;

/**
 * Class represents asset from string
 */
class InlineAsset extends AbstractAsset
{
    /**
     * @var int|null
     */
    private $lastModified;

    /**
     * Constructor
     *
     * @param string   $content
     * @param string[] $dependencies
     * @param array    $extras
     */
    public function __construct($content, array $dependencies = [], array $extras = [])
    {
        $this->setContent($content);
        parent::__construct($dependencies, $extras);
    }

    /**
     * @param int|null $lastModified
     */
    public function setLastModified($lastModified)
    {
        $this->lastModified = $lastModified;
    }

    /**
     * @inheritDoc
     */
    public function getLastModified()
    {
        return $this->lastModified;
    }

    /**
     * @inheritDoc
     */
    public function getHash()
    {
        return md5(static::class . $this->getContent());
    }
}