<?php

require_once __DIR__ . "/../vendor/autoload.php";

use srag\DevTools\SrContainerObjectMenu\DevToolsCtrl;
use srag\DIC\SrContainerObjectMenu\DICTrait;
use srag\Plugins\SrContainerObjectMenu\Area\AreasCtrl;
use srag\Plugins\SrContainerObjectMenu\Config\ConfigCtrl;
use srag\Plugins\SrContainerObjectMenu\ContainerObject\ContainerObjectsCtrl;
use srag\Plugins\SrContainerObjectMenu\Utils\SrContainerObjectMenuTrait;

/**
 * Class ilSrContainerObjectMenuConfigGUI
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\DevTools\SrContainerObjectMenu\DevToolsCtrl: ilSrContainerObjectMenuConfigGUI
 */
class ilSrContainerObjectMenuConfigGUI extends ilPluginConfigGUI
{

    use DICTrait;
    use SrContainerObjectMenuTrait;

    const CMD_CONFIGURE = "configure";
    const PLUGIN_CLASS_NAME = ilSrContainerObjectMenuPlugin::class;


    /**
     * ilSrContainerObjectMenuConfigGUI constructor
     */
    public function __construct()
    {

    }


    /**
     * @inheritDoc
     */
    public function performCommand(/*string*/ $cmd)/* : void*/
    {
        $this->setTabs();

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            case strtolower(AreasCtrl::class):
                self::dic()->ctrl()->forwardCommand(new AreasCtrl());
                break;

            case strtolower(ConfigCtrl::class):
                self::dic()->ctrl()->forwardCommand(new ConfigCtrl());
                break;

            case strtolower(ContainerObjectsCtrl::class):
                self::dic()->ctrl()->forwardCommand(new ContainerObjectsCtrl());
                break;

            case strtolower(DevToolsCtrl::class):
                self::dic()->ctrl()->forwardCommand(new DevToolsCtrl($this, self::plugin()));
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
    protected function configure()/* : void*/
    {
        self::dic()->ctrl()->redirectByClass(ContainerObjectsCtrl::class, ContainerObjectsCtrl::CMD_LIST_CONTAINER_OBJECTS);
    }


    /**
     *
     */
    protected function setTabs()/* : void*/
    {
        ContainerObjectsCtrl::addTabs();

        AreasCtrl::addTabs();

        ConfigCtrl::addTabs();

        DevToolsCtrl::addTabs(self::plugin());

        self::dic()->locator()->addItem(ilSrContainerObjectMenuPlugin::PLUGIN_NAME, self::dic()->ctrl()->getLinkTarget($this, self::CMD_CONFIGURE));
    }
}
