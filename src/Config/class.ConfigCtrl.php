<?php

namespace srag\Plugins\SrContainerObjectMenu\Config;

require_once __DIR__ . "/../../vendor/autoload.php";

use ilSrContainerObjectMenuPlugin;
use ilUtil;
use srag\DIC\SrContainerObjectMenu\DICTrait;
use srag\Plugins\SrContainerObjectMenu\Utils\SrContainerObjectMenuTrait;

/**
 * Class ConfigCtrl
 *
 * @package           srag\Plugins\SrContainerObjectMenu\Config
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrContainerObjectMenu\Config\ConfigCtrl: ilSrContainerObjectMenuConfigGUI
 */
class ConfigCtrl
{

    use DICTrait;
    use SrContainerObjectMenuTrait;

    const CMD_EDIT_CONFIG = "editConfig";
    const CMD_UPDATE_CONFIG = "updateConfig";
    const LANG_MODULE = "config";
    const PLUGIN_CLASS_NAME = ilSrContainerObjectMenuPlugin::class;
    const TAB_EDIT_CONFIG = "edit_config";
    /**
     * @var Config
     */
    protected $config;


    /**
     * ConfigCtrl constructor
     */
    public function __construct()
    {

    }


    /**
     *
     */
    public static function addTabs() : void
    {
        self::dic()
            ->tabs()
            ->addTab(self::TAB_EDIT_CONFIG, self::plugin()->translate("configuration", self::LANG_MODULE),
                self::dic()->ctrl()->getLinkTargetByClass(self::class, self::CMD_EDIT_CONFIG));
    }


    /**
     *
     */
    public function executeCommand() : void
    {
        $this->config = self::srContainerObjectMenu()->config()->getConfig();

        $this->setTabs();

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            default:
                $cmd = self::dic()->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_EDIT_CONFIG:
                    case self::CMD_UPDATE_CONFIG:
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
    protected function editConfig() : void
    {
        self::dic()->tabs()->activateTab(self::TAB_EDIT_CONFIG);

        $form = self::srContainerObjectMenu()->config()->factory()->newFormBuilderInstance($this, $this->config);

        self::output()->output($form);
    }


    /**
     *
     */
    protected function setTabs() : void
    {

    }


    /**
     *
     */
    protected function updateConfig() : void
    {
        self::dic()->tabs()->activateTab(self::TAB_EDIT_CONFIG);

        $form = self::srContainerObjectMenu()->config()->factory()->newFormBuilderInstance($this, $this->config);

        if (!$form->storeForm()) {
            self::output()->output($form);

            return;
        }

        ilUtil::sendSuccess(self::plugin()->translate("configuration_saved", self::LANG_MODULE), true);

        self::dic()->ctrl()->redirect($this, self::CMD_EDIT_CONFIG);
    }
}
