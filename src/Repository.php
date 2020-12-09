<?php

namespace srag\Plugins\SrContainerObjectMenu;

use ilSrContainerObjectMenuPlugin;
use srag\DIC\SrContainerObjectMenu\DICTrait;
use srag\Plugins\SrContainerObjectMenu\Area\Repository as AreasRepository;
use srag\Plugins\SrContainerObjectMenu\ContainerObject\Repository as ContainerObjectsRepository;
use srag\Plugins\SrContainerObjectMenu\Menu\Repository as MenuRepository;
use srag\Plugins\SrContainerObjectMenu\SelectedArea\Repository as SelectedAreaRepository;
use srag\Plugins\SrContainerObjectMenu\Utils\SrContainerObjectMenuTrait;

/**
 * Class Repository
 *
 * @package srag\Plugins\SrContainerObjectMenu
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Repository
{

    use DICTrait;
    use SrContainerObjectMenuTrait;

    const PLUGIN_CLASS_NAME = ilSrContainerObjectMenuPlugin::class;
    /**
     * @var self|null
     */
    protected static $instance = null;


    /**
     * Repository constructor
     */
    private function __construct()
    {

    }


    /**
     * @return self
     */
    public static function getInstance() : self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * @return AreasRepository
     */
    public function areas() : AreasRepository
    {
        return AreasRepository::getInstance();
    }


    /**
     * @return ContainerObjectsRepository
     */
    public function containerObjects() : ContainerObjectsRepository
    {
        return ContainerObjectsRepository::getInstance();
    }


    /**
     *
     */
    public function dropTables()/*: void*/
    {
        $this->menu()->factory()->instance()->ensureProvideNoItems();

        $this->areas()->dropTables();
        $this->containerObjects()->dropTables();
        $this->menu()->dropTables();
        $this->selectedArea()->dropTables();
    }


    /**
     *
     */
    public function installTables()/*: void*/
    {
        $this->menu()->factory()->instance()->ensureProvideNoItems();

        $this->areas()->installTables();
        $this->containerObjects()->installTables();
        $this->menu()->installTables();
        $this->selectedArea()->installTables();
    }


    /**
     * @return MenuRepository
     */
    public function menu() : MenuRepository
    {
        return MenuRepository::getInstance();
    }


    /**
     * @return SelectedAreaRepository
     */
    public function selectedArea() : SelectedAreaRepository
    {
        return SelectedAreaRepository::getInstance();
    }
}
