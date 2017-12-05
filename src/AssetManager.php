<?php

namespace PE\Component\Asset;

use PE\Component\Asset\Asset\AssetCollection;
use PE\Component\Asset\Exception\ExceptionInterface;
use PE\Component\Asset\Exception\InvalidArgumentException;
use PE\Component\Asset\Exception\UnexpectedValueException;
use PE\Component\Asset\Renderer\RendererInterface;

class AssetManager
{
    /**
     * @var AssetCollection[]
     */
    protected $collections = [];

    /**
     * @var RendererInterface[]
     */
    protected $renderers = [];

    /**
     * @var bool
     */
    protected $autoCreate = true;

    /**
     * Constructor
     *
     * @param AssetCollection[]   $collections
     * @param RendererInterface[] $renderers
     * @param bool                $autoCreate
     *
     * @throws ExceptionInterface
     */
    public function __construct(array $collections = [], array $renderers = [], $autoCreate = true)
    {
        UnexpectedValueException::validate($collections, AssetCollection::class . '[]');
        UnexpectedValueException::validate($renderers, RendererInterface::class . '[]');

        $this->collections = $collections;
        $this->renderers   = $renderers;
        $this->autoCreate  = (bool) $autoCreate;
    }

    /**
     * @param string $name
     *
     * @return AssetCollection
     *
     * @throws ExceptionInterface
     */
    public function get($name)
    {
        UnexpectedValueException::validate($name, 'string');

        if (array_key_exists($name, $this->collections)) {
            return $this->collections[$name];
        }

        if ($this->autoCreate) {
            return $this->collections[$name] = new AssetCollection();
        }

        throw new InvalidArgumentException(sprintf('Asset collection "%s" not found', $name));
    }

    /**
     * @param string     $name
     * @param AssetCollection $collection
     *
     * @return $this
     *
     * @throws ExceptionInterface
     */
    public function set($name, AssetCollection $collection)
    {
        UnexpectedValueException::validate($name, 'string');

        $this->collections[$name] = $collection;
        return $this;
    }

    /**
     * @param string $name
     *
     * @return string
     *
     * @throws ExceptionInterface
     */
    public function render($name)
    {
        UnexpectedValueException::validate($name, 'string');

        if (array_key_exists($name, $this->renderers)) {
            return $this->renderers[$name]->render($this->get($name));
        }

        return '';
    }
}