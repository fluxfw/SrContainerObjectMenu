<?php

namespace srag\DIC\SrContainerObjectMenu\Plugin;

/**
 * Interface Pluginable
 *
 * @package srag\DIC\SrContainerObjectMenu\Plugin
 */
interface Pluginable
{

    /**
     * @return PluginInterface
     */
    public function getPlugin() : PluginInterface;


    /**
     * @param PluginInterface $plugin
     *
     * @return static
     */
    public function withPlugin(PluginInterface $plugin)/*: static*/ ;
}
