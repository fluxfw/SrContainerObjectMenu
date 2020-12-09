<?php

namespace srag\Plugins\SrContainerObjectMenu\ContainerObject;

use ilSrContainerObjectMenuPlugin;
use srag\DIC\SrContainerObjectMenu\DICTrait;
use srag\Plugins\SrContainerObjectMenu\Utils\SrContainerObjectMenuTrait;

/**
 * Class ContainerObjectsCtrl
 *
 * @package           srag\Plugins\SrContainerObjectMenu\ContainerObject
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrContainerObjectMenu\ContainerObject\ContainerObjectsCtrl: ilSrContainerObjectMenuConfigGUI
 */
class ContainerObjectsCtrl
{

    use DICTrait;
    use SrContainerObjectMenuTrait;

    const CMD_LIST_CONTAINER_OBJECTS = "listContainerObjects";
    const LANG_MODULE = "container_objects";
    const PLUGIN_CLASS_NAME = ilSrContainerObjectMenuPlugin::class;
    const TAB_LIST_CONTAINER_OBJECTS = "list_container_objects";


    /**
     * ContainerObjectsCtrl constructor
     */
    public function __construct()
    {

    }


    /**
     *
     */
    public static function addTabs()/*: void*/
    {
        self::dic()->tabs()->addTab(self::TAB_LIST_CONTAINER_OBJECTS, self::plugin()->translate("container_objects", self::LANG_MODULE), self::dic()->ctrl()
            ->getLinkTargetByClass(self::class, self::CMD_LIST_CONTAINER_OBJECTS));
    }


    /**
     *
     */
    public function executeCommand()/*: void*/
    {
        $this->setTabs();

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            case strtolower(ContainerObjectCtrl::class):
                self::dic()->ctrl()->forwardCommand(new ContainerObjectCtrl());
                break;

            default:
                $cmd = self::dic()->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_LIST_CONTAINER_OBJECTS:
                        $this->{$cmd}();
                        break;

                    default:
                        break;
                }
                break;
        }
    }


    /**
     *
     */
    protected function listContainerObjects()/*: void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_LIST_CONTAINER_OBJECTS);

        $table = self::srContainerObjectMenu()->containerObjects()->factory()->newTableBuilderInstance($this);

        self::output()->output($table);
    }


    /**
     *
     */
    protected function setTabs()/*: void*/
    {

    }
}
