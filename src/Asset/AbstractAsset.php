<?php

namespace PE\Component\Asset\Asset;

abstract class AbstractAsset implements AssetInterface
{
    /**
     * @var string[]
     */
    private $dependencies = [];

    /**
     * @var array
     */
    private $extras = [];

    /**
     * @var string
     */
    private $uri;

    /**
     * @var string
     */
    private $rootURI;

    /**
     * @var string
     */
    private $rootDIR;

    /**
     * @var string
     */
    private $content;

    /**
     * @param string[] $dependencies
     * @param array    $extras
     */
    public function __construct(array $dependencies = [], array $extras = [])
    {
        $this->dependencies = $dependencies;
        $this->extras       = $extras;
    }

    /**
     * @inheritdoc
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @inheritdoc
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @inheritDoc
     */
    public function getDependencies()
    {
        return $this->dependencies;
    }

    /**
     * @inheritDoc
     */
    public function getExtras()
    {
        return $this->extras;
    }

    /**
     * @inheritDoc
     */
    public function getExtra($name, $default = null)
    {
        return array_key_exists($name, $this->extras) ? $this->extras[$name] : $default;
    }

    public function setURI($uri)
    {
        $this->uri = $uri;
    }

    /**
     * @inheritdoc
     */
    public function getURI()
    {
        return $this->uri;
    }

    /**
     * @inheritdoc
     */
    public function getRootURI()
    {
        return $this->rootURI;
    }

    /**
     * @inheritdoc
     */
    public function setRootURI($rootURI)
    {
        $this->rootURI = $rootURI;
    }

    /**
     * @inheritdoc
     */
    public function getRootDIR()
    {
        return $this->rootDIR;
    }

    /**
     * @inheritdoc
     */
    public function setRootDIR($rootDIR)
    {
        $this->rootDIR = $rootDIR;
    }
}