<?php

namespace srag\Plugins\SrContainerObjectMenu\SelectedArea;

require_once __DIR__ . "/../../vendor/autoload.php";

use ilSrContainerObjectMenuPlugin;
use srag\DIC\SrContainerObjectMenu\DICTrait;
use srag\Plugins\SrContainerObjectMenu\Utils\SrContainerObjectMenuTrait;

/**
 * Class SelectAreaCtrl
 *
 * @package           srag\Plugins\SrContainerObjectMenu\SelectedArea
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrContainerObjectMenu\SelectedArea\SelectAreaCtrl: ilUIPluginRouterGUI
 */
class SelectAreaCtrl
{

    use DICTrait;
    use SrContainerObjectMenuTrait;

    const CMD_SELECT_AREA = "selectArea";
    const GET_PARAM_AREA_ID = "area_id";
    const LANG_MODULE = "selected_area";
    const PLUGIN_CLASS_NAME = ilSrContainerObjectMenuPlugin::class;
    /**
     * @var int
     */
    protected $area_id;
    /**
     * @var SelectedArea
     */
    protected $selected_area;


    /**
     * SelectAreaCtrl constructor
     */
    public function __construct()
    {

    }


    /**
     *
     */
    public function executeCommand()/* : void*/
    {
        $this->area_id = intval(filter_input(INPUT_GET, self::GET_PARAM_AREA_ID));

        self::dic()->ctrl()->saveParameter($this, self::GET_PARAM_AREA_ID);

        $this->selected_area = self::srContainerObjectMenu()->selectedArea()->getSelectedArea(self::dic()->user()->getId());

        $this->setTabs();

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            default:
                $cmd = self::dic()->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_SELECT_AREA:
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
    protected function selectArea()/* : void*/
    {
        $this->selected_area->setAreaId($this->area_id);

        self::srContainerObjectMenu()->selectedArea()->storeSelectedArea($this->selected_area);

        $back_url = strval(filter_input(INPUT_SERVER, "HTTP_REFERER"));
        if (empty($back_url)) {
            $back_url = "/";
        }

        self::dic()->ctrl()->redirectToURL($back_url);
    }


    /**
     *
     */
    protected function setTabs()/* : void*/
    {

    }
}
