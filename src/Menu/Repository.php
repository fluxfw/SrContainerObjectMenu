<?php

namespace srag\Plugins\SrContainerObjectMenu\Menu;

use ActiveRecord;
use ilDBConstants;
use ilGSIdentificationStorage;
use ilMMItemFacadeInterface;
use ilMMItemStorage;
use ilMMItemTranslationStorage;
use ilSrContainerObjectMenuPlugin;
use srag\DIC\SrContainerObjectMenu\DICTrait;
use srag\Plugins\SrContainerObjectMenu\Utils\SrContainerObjectMenuTrait;

/**
 * Class Repository
 *
 * @package srag\Plugins\SrContainerObjectMenu\Menu
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
     * @var ilMMItemFacadeInterface[]
     */
    protected $menu_items = [];


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
     * @param string $base_menu_identifier
     * @param bool   $strict
     */
    public function deleteMenuItems(string $base_menu_identifier, bool $strict = true)/* : void*/
    {
        if (method_exists("flushLostItems", self::dic()->mainMenuItem())) {
            self::dic()->mainMenuItem()->flushLostItems();
        }

        /**
         * @var ActiveRecord $ar_class
         */
        foreach ([ilGSIdentificationStorage::class, ilMMItemStorage::class, ilMMItemTranslationStorage::class] as $ar_class) {
            if (!class_exists($ar_class)) {
                continue;
            }

            if ($strict) {
                $where = $ar_class::where('(' . self::dic()->database()->like("identification", ilDBConstants::T_TEXT, '%' . $base_menu_identifier) . ' OR ' . self::dic()
                        ->database()
                        ->like("identification", ilDBConstants::T_TEXT, '%' . $base_menu_identifier . '_%') . ')');
            } else {
                $where = $ar_class::where([
                    "identification" => '%' . $base_menu_identifier . '%'
                ], "LIKE");
            }

            foreach ($where->get() as $menu_item) {
                $menu_item->delete();
            }
        }

        $this->menu_items = [];
    }


    /**
     * @internal
     */
    public function dropTables()/* : void*/
    {
        $this->deleteMenuItems(self::srContainerObjectMenu()->containerObjects()->factory()->newInstance()->getMenuIdentifier(), false);
    }


    /**
     * @return Factory
     */
    public function factory() : Factory
    {
        return Factory::getInstance();
    }


    /**
     * @param string $menu_identifier
     *
     * @return ilMMItemFacadeInterface|null
     */
    public function getMenuItem(string $menu_identifier)/* : ?ilMMItemFacadeInterface*/
    {
        if ($this->menu_items[$menu_identifier] === null) {
            $identifications = ilMMItemStorage::where([
                "identification" => '%' . $menu_identifier
            ], "LIKE")->getArray(null, "identification");

            $identification = end($identifications);

            if ($identification) {
                $this->menu_items[$menu_identifier] = self::dic()->mainMenuItem()->getItemFacadeForIdentificationString($identification);
            } else {
                $this->menu_items[$menu_identifier] = false;
            }
        }

        return ($this->menu_items[$menu_identifier] ?: null);
    }


    /**
     * @internal
     */
    public function installTables()/* : void*/
    {

    }
}
