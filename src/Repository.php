<?php

namespace srag\Plugins\SrContainerObjectMenu;

use ilSrContainerObjectMenuPlugin;
use srag\DataTable\SrContainerObjectMenu\Component\Factory as DataTableFactoryInterface;
use srag\DataTable\SrContainerObjectMenu\Utils\DataTableTrait;
use srag\DIC\SrContainerObjectMenu\DICTrait;
use srag\Plugins\SrContainerObjectMenu\ContainerObject\Repository as ContainerObjectsRepository;
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
    use DataTableTrait {
        dataTable as protected _dataTable;
    }
    const PLUGIN_CLASS_NAME = ilSrContainerObjectMenuPlugin::class;
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
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * Repository constructor
     */
    private function __construct()
    {

    }


    /**
     * @return ContainerObjectsRepository
     */
    public function containerObjects() : ContainerObjectsRepository
    {
        return ContainerObjectsRepository::getInstance();
    }


    /**
     * @inheritDoc
     */
    public function dataTable() : DataTableFactoryInterface
    {
        return self::_dataTable();
    }


    /**
     *
     */
    public function dropTables()/*: void*/
    {
        $this->containerObjects()->dropTables();
    }


    /**
     *
     */
    public function installTables()/*: void*/
    {
        $this->containerObjects()->installTables();
    }
}
