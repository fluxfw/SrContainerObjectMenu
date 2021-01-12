<?php

namespace srag\Plugins\SrContainerObjectMenu\Menu;

use ActiveRecord;
use ilDBConstants;
use ilGSIdentificationStorage;
use ILIAS\GlobalScreen\Scope\MainMenu\Factory\Item\Lost;
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
    protected $base_menu_items = [];
    /**
     * @var string[]
     */
    protected $menu_css_identifier = [];
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
     * @return int
     */
    public function cleanUpLostItems() : int
    {
        return $this->deleteMenuItems(self::srContainerObjectMenu()->containerObjects()->getMenuIdentifier(), true, true);
    }


    /**
     * @param string $base_menu_identifier
     * @param bool   $strict
     * @param bool   $only_lost_items
     *
     * @return int
     */
    public function deleteMenuItems(string $base_menu_identifier, bool $strict = true, bool $only_lost_items = false) : int
    {
        $count = 0;

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

            foreach ($where->get() as $item) {
                if ($only_lost_items) {
                    if (!$this->isLostMenuItem($this->getMenuItem($item->getIdentification()))) {
                        continue;
                    }
                }

                $item->delete();

                $count++;
            }
        }

        $this->base_menu_items = [];
        $this->menu_items = [];

        return $count;
    }


    /**
     * @internal
     */
    public function dropTables()/* : void*/
    {
        $this->deleteMenuItems(self::srContainerObjectMenu()->containerObjects()->getMenuIdentifier(), false);
    }


    /**
     * @return Factory
     */
    public function factory() : Factory
    {
        return Factory::getInstance();
    }


    /**
     * @param string $base_menu_identifier
     *
     * @return ilMMItemFacadeInterface|null
     */
    public function getBaseMenuItem(string $base_menu_identifier)/* : ?ilMMItemFacadeInterface*/
    {
        if ($this->base_menu_items[$base_menu_identifier] === null) {
            $identifications = ilMMItemStorage::where([
                "identification" => '%' . $base_menu_identifier
            ], "LIKE")->getArray(null, "identification");

            $identification = end($identifications);

            if ($identification) {
                $this->base_menu_items[$base_menu_identifier] = $this->getMenuItem($identification);
            } else {
                $this->base_menu_items[$base_menu_identifier] = false;
            }
        }

        return ($this->base_menu_items[$base_menu_identifier] ?: null);
    }


    /**
     * @param string $menu_identifier
     *
     * @return string
     */
    public function getMenuCSSIdentifier(string $menu_identifier) : string
    {
        if ($this->menu_css_identifier[$menu_identifier] === null) {
            $this->menu_css_identifier[$menu_identifier] = "#mm_" . $menu_identifier;
        }

        return $this->menu_css_identifier[$menu_identifier];
    }


    /**
     * @param string $menu_identifier
     *
     * @return ilMMItemFacadeInterface
     */
    public function getMenuItem(string $menu_identifier) : ilMMItemFacadeInterface
    {
        if ($this->menu_items[$menu_identifier] === null) {
            $this->menu_items[$menu_identifier] = self::dic()->mainMenuItem()->getItemFacadeForIdentificationString($menu_identifier);
        }

        return $this->menu_items[$menu_identifier];
    }


    /**
     * @internal
     */
    public function installTables()/* : void*/
    {

    }


    /**
     * @param ilMMItemFacadeInterface $menu_item
     *
     * @return bool
     */
    public function isLostMenuItem(ilMMItemFacadeInterface $menu_item) : bool
    {
        return $menu_item->item() instanceof Lost;
    }
}
