<?php

require_once __DIR__ . "/../vendor/autoload.php";

use srag\DIC\SrContainerObjectMenu\DICTrait;
use srag\Plugins\SrContainerObjectMenu\ContainerObject\ContainerObjectsGUI;
use srag\Plugins\SrContainerObjectMenu\Utils\SrContainerObjectMenuTrait;

/**
 * Class ilSrContainerObjectMenuConfigGUI
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ilSrContainerObjectMenuConfigGUI extends ilPluginConfigGUI
{

    use DICTrait;
    use SrContainerObjectMenuTrait;

    const PLUGIN_CLASS_NAME = ilSrContainerObjectMenuPlugin::class;
    const CMD_CONFIGURE = "configure";


    /**
     * ilSrContainerObjectMenuConfigGUI constructor
     */
    public function __construct()
    {

    }


    /**
     * @inheritDoc
     */
    public function performCommand(/*string*/ $cmd)/*:void*/
    {
        $this->setTabs();

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            case strtolower(ContainerObjectsGUI::class):
                self::dic()->ctrl()->forwardCommand(new ContainerObjectsGUI());
                break;

            default:
                $cmd = self::dic()->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_CONFIGURE:
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
    protected function setTabs()/*: void*/
    {
        ContainerObjectsGUI::addTabs();

        self::dic()->locator()->addItem(ilSrContainerObjectMenuPlugin::PLUGIN_NAME, self::dic()->ctrl()->getLinkTarget($this, self::CMD_CONFIGURE));
    }


    /**
     *
     */
    protected function configure()/*: void*/
    {
        self::dic()->ctrl()->redirectByClass(ContainerObjectsGUI::class, ContainerObjectsGUI::CMD_LIST_CONTAINER_OBJECTS);
    }
}
