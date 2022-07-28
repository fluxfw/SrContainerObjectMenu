<?php

namespace srag\Plugins\SrContainerObjectMenu\Config;

use ilSrContainerObjectMenuPlugin;
use srag\DIC\SrContainerObjectMenu\DICTrait;
use srag\Plugins\SrContainerObjectMenu\SelectedArea\SelectAreaCtrl;
use srag\Plugins\SrContainerObjectMenu\SelectedArea\SelectedArea;
use srag\Plugins\SrContainerObjectMenu\Utils\SrContainerObjectMenuTrait;

/**
 * Class Repository
 *
 * @package srag\Plugins\SrContainerObjectMenu\Config
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
     * @var Config|null
     */
    protected $config = null;


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
     * @internal
     */
    public function dropTables() : void
    {
        self::dic()->database()->dropTable(Config::TABLE_NAME, false);
    }


    /**
     * @return Factory
     */
    public function factory() : Factory
    {
        return Factory::getInstance();
    }


    /**
     * @return Config
     */
    public function getConfig() : Config
    {
        if ($this->config === null) {
            $this->config = Config::first();

            if ($this->config === null) {
                $this->config = $this->factory()->newInstance();
            }
        }

        return $this->config;
    }


    /**
     * @internal
     */
    public function installTables() : void
    {
        Config::updateDB();

        $config = $this->getConfig();

        if (empty($config->getSelectedAreaMenuTitles())) {
            $config->setSelectedAreaMenuTitle(self::plugin()->translate("menu_title_template", SelectAreaCtrl::LANG_MODULE, [SelectedArea::AREA_TITLE_PLACEHOLDER], true, "en"),
                "default");
            $config->setSelectedAreaMenuTitle(self::plugin()->translate("menu_title_template", SelectAreaCtrl::LANG_MODULE, [SelectedArea::AREA_TITLE_PLACEHOLDER], true, "en"), "en");
            $config->setSelectedAreaMenuTitle(self::plugin()->translate("menu_title_template", SelectAreaCtrl::LANG_MODULE, [SelectedArea::AREA_TITLE_PLACEHOLDER], true, "de"), "de");
            $this->storeConfig($config);
        }
    }


    /**
     * @param Config $config
     */
    public function storeConfig(Config $config) : void
    {
        $config->store();

        $this->config = $config;
    }
}
