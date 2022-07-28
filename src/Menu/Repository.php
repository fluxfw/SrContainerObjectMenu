<?php

namespace srag\Plugins\SrContainerObjectMenu\Menu;

use ActiveRecord;
use ilDBConstants;
use ilGSIdentificationStorage;
use ILIAS\GlobalScreen\Identification\IdentificationInterface;
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
     * @var string[]
     */
    protected $base_menu_identifiers = [];
    /**
     * @var string[]
     */
    protected $menu_css_identifier = [];
    /**
     * @var IdentificationInterface[]
     */
    protected $menu_identifications = [];
    /**
     * @var ilMMItemFacadeInterface[]
     */
    protected $menu_items = [];
    /**
     * @var string[]
     */
    protected $menu_titles = [];


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

        $this->base_menu_identifiers = [];
        $this->menu_identifications = [];
        $this->menu_items = [];
        $this->menu_titles = [];

        return $count;
    }


    /**
     * @internal
     */
    public function dropTables() : void
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
     * @return string|null
     */
    public function getBaseMenuIdentifier(string $base_menu_identifier) : ?string
    {
        if ($this->base_menu_identifiers[$base_menu_identifier] === null) {
            $identifications = ilMMItemStorage::where([
                "identification" => '%' . $base_menu_identifier
            ], "LIKE")->getArray(null, "identification");

            $menu_identifier = end($identifications);

            if ($menu_identifier) {
                $this->base_menu_identifiers[$base_menu_identifier] = $menu_identifier;
            } else {
                $this->base_menu_identifiers[$base_menu_identifier] = false;
            }
        }

        return ($this->base_menu_identifiers[$base_menu_identifier] ?: null);
    }


    /**
     * @param string $menu_identifier
     * @param bool   $css_selector
     *
     * @return string
     */
    public function getMenuCSSIdentifier(string $menu_identifier, bool $css_selector = true) : string
    {
        $cache_key = $menu_identifier . "_" . intval($css_selector);

        if ($this->menu_css_identifier[$cache_key] === null) {
            if ($css_selector) {
                $this->menu_css_identifier[$cache_key] = "." . $this->getMenuCSSIdentifier($menu_identifier, false);
            } else {
                $this->menu_css_identifier[$cache_key] = "mm_" . $menu_identifier;
            }
        }

        return $this->menu_css_identifier[$cache_key];
    }


    /**
     * @param string $menu_identifier
     *
     * @return IdentificationInterface
     */
    public function getMenuIdentification(string $menu_identifier) : IdentificationInterface
    {
        if ($this->menu_identifications[$menu_identifier] === null) {
            $this->menu_identifications[$menu_identifier] = self::dic()->globalScreen()->identification()->fromSerializedIdentification($menu_identifier);
        }

        return $this->menu_identifications[$menu_identifier];
    }


    /**
     * @param string $menu_identifier
     *
     * @return ilMMItemFacadeInterface
     */
    public function getMenuItem(string $menu_identifier) : ilMMItemFacadeInterface
    {
        if ($this->menu_items[$menu_identifier] === null) {
            $this->menu_items[$menu_identifier] = self::dic()->mainMenuItem()->getItemFacade($this->getMenuIdentification($menu_identifier));
        }

        return $this->menu_items[$menu_identifier];
    }


    /**
     * @param string $base_menu_identifier
     *
     * @return String
     */
    public function getMenuTitle(string $base_menu_identifier) : string
    {
        if ($this->menu_titles[$base_menu_identifier] === null) {
            $menu_identifier = $this->getBaseMenuIdentifier($base_menu_identifier);

            if (!empty($menu_identifier)) {
                $this->menu_titles[$base_menu_identifier] = ilMMItemTranslationStorage::getDefaultTranslation($this->getMenuIdentification($menu_identifier));
            } else {
                $this->menu_titles[$base_menu_identifier] = "";
            }
        }

        return $this->menu_titles[$base_menu_identifier];
    }


    /**
     * @internal
     */
    public function installTables() : void
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
