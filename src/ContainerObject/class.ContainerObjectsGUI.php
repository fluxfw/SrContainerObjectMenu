<?php

namespace srag\Plugins\SrContainerObjectMenu\ContainerObject;

use ilSrContainerObjectMenuPlugin;
use srag\DIC\SrContainerObjectMenu\DICTrait;
use srag\Plugins\SrContainerObjectMenu\Utils\SrContainerObjectMenuTrait;

/**
 * Class ContainerObjectsGUI
 *
 * @package           srag\Plugins\SrContainerObjectMenu\ContainerObject
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrContainerObjectMenu\ContainerObject\ContainerObjectsGUI: ilSrContainerObjectMenuConfigGUI
 */
class ContainerObjectsGUI
{

    use DICTrait;
    use SrContainerObjectMenuTrait;
    const PLUGIN_CLASS_NAME = ilSrContainerObjectMenuPlugin::class;
    const CMD_LIST_CONTAINER_OBJECTS = "listContainerObjects";
    const LANG_MODULE = "container_objects";
    const TAB_LIST_CONTAINER_OBJECTS = "list_container_objects";


    /**
     * ContainerObjectsGUI constructor
     */
    public function __construct()
    {

    }


    /**
     *
     */
    public function executeCommand()/*: void*/
    {
        $this->setTabs();

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            case strtolower(ContainerObjectGUI::class):
                self::dic()->ctrl()->forwardCommand(new ContainerObjectGUI());
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
    public static function addTabs()/*: void*/
    {
        self::dic()->tabs()->addTab(self::TAB_LIST_CONTAINER_OBJECTS, self::plugin()->translate("container_objects", self::LANG_MODULE), self::dic()->ctrl()
            ->getLinkTargetByClass(self::class, self::CMD_LIST_CONTAINER_OBJECTS));
    }


    /**
     *
     */
    protected function setTabs()/*: void*/
    {

    }


    /**
     *
     */
    protected function listContainerObjects()/*: void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_LIST_CONTAINER_OBJECTS);

        $table = self::srContainerObjectMenu()->containerObjects()->factory()->newTableInstance();

        self::output()->output($table);
    }
}
