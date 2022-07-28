<?php

namespace srag\Plugins\SrContainerObjectMenu\Area;

require_once __DIR__ . "/../../vendor/autoload.php";

use ilSrContainerObjectMenuPlugin;
use srag\DIC\SrContainerObjectMenu\DICTrait;
use srag\Plugins\SrContainerObjectMenu\Utils\SrContainerObjectMenuTrait;

/**
 * Class AreasCtrl
 *
 * @package           srag\Plugins\SrContainerObjectMenu\Area
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrContainerObjectMenu\Area\AreasCtrl: ilSrContainerObjectMenuConfigGUI
 */
class AreasCtrl
{

    use DICTrait;
    use SrContainerObjectMenuTrait;

    const CMD_LIST_AREAS = "listAreas";
    const LANG_MODULE = "areas";
    const PLUGIN_CLASS_NAME = ilSrContainerObjectMenuPlugin::class;
    const TAB_LIST_AREAS = "list_areas";


    /**
     * AreasCtrl constructor
     */
    public function __construct()
    {

    }


    /**
     *
     */
    public static function addTabs() : void
    {
        self::dic()->tabs()->addTab(self::TAB_LIST_AREAS, self::plugin()->translate("areas", self::LANG_MODULE), self::dic()->ctrl()
            ->getLinkTargetByClass(self::class, self::CMD_LIST_AREAS));
    }


    /**
     *
     */
    public function executeCommand() : void
    {
        $this->setTabs();

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            case strtolower(AreaCtrl::class):
                self::dic()->ctrl()->forwardCommand(new AreaCtrl());
                break;

            default:
                $cmd = self::dic()->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_LIST_AREAS:
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
    protected function listAreas() : void
    {
        self::dic()->tabs()->activateTab(self::TAB_LIST_AREAS);

        $table = self::srContainerObjectMenu()->areas()->factory()->newTableBuilderInstance($this);

        self::output()->output($table);
    }


    /**
     *
     */
    protected function setTabs() : void
    {

    }
}
