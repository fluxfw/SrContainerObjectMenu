<?php

namespace srag\Plugins\SrContainerObjectMenu\ContainerObject;

use srag\Plugins\SrContainerObjectMenu\Menu\BaseMenu;

/**
 * Class Menu
 *
 * @package srag\Plugins\SrContainerObjectMenu\ContainerObject
 */
class Menu extends BaseMenu
{

    /**
     * @var self|null
     */
    protected static $instance = null;


    /**
     * @return self
     */
    public static function getInstance() : self
    {
        if (self::$instance === null) {
            self::$instance = new self(self::dic()->dic(), self::plugin()->getPluginObject());
        }

        return self::$instance;
    }
}
