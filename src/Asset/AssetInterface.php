<?php

namespace PE\Component\Asset\Asset;

interface AssetInterface
{
    /**
     * Get asset content
     *
     * @return string|null
     */
    public function getContent();

    /**
     * Get asset content
     *
     * @param string|null $content
     */
    public function setContent($content);

    /**
     * Get asset names on which current asset dependent
     *
     * @return string[]
     */
    public function getDependencies();

    /**
     * @return array
     */
    public function getExtras();

    /**
     * Get asset attributes
     *
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getExtra($name, $default = null);

    /**
     * @return string
     */
    public function getURI();

    /**
     * @return string
     */
    public function getRootURI();

    /**
     * @param string $rootURI
     */
    public function setRootURI($rootURI);

    /**
     * @return string
     */
    public function getRootDIR();

    /**
     * @param string $rootDIR
     */
    public function setRootDIR($rootDIR);

    /**
     * Get the time the current asset was last modified.
     *
     * @return integer|null A UNIX timestamp
     */
    public function getLastModified();

    /**
     * Get asset unique hash
     *
     * @return string|null
     */
    public function getHash();
}