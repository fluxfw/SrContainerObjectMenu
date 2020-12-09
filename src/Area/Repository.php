<?php

namespace srag\Plugins\SrContainerObjectMenu\Area;

use ilSrContainerObjectMenuPlugin;
use srag\DIC\SrContainerObjectMenu\DICTrait;
use srag\Plugins\SrContainerObjectMenu\Utils\SrContainerObjectMenuTrait;

/**
 * Class Repository
 *
 * @package srag\Plugins\SrContainerObjectMenu\Area
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
     * @var Area[]
     */
    protected $areas = [];
    /**
     * @var Area[]
     */
    protected $areas_by_id = [];


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
     * @param Area $area
     */
    public function deleteArea(Area $area)/*: void*/
    {
        $area->delete();

        foreach (self::srContainerObjectMenu()->containerObjects()->getContainerObjects($area->getAreaId()) as $container_object) {
            $container_object->setAreaIds(array_values(array_filter($container_object->getAreaIds(), function (int $area_id) use ($area) : bool {
                return ($area_id !== $area->getAreaId());
            })));

            self::srContainerObjectMenu()->containerObjects()->storeContainerObject($container_object);
        }

        foreach (self::srContainerObjectMenu()->selectedArea()->getSelectedAreas() as $selected_area) {
            if ($selected_area->getAreaId(true) === $area->getAreaId()) {
                $selected_area->setAreaId();

                self::srContainerObjectMenu()->selectedArea()->storeSelectedArea($selected_area);
            }
        }

        self::srContainerObjectMenu()->menu()->deleteMenuItems($area->getMenuIdentifier());

        unset($this->areas_by_id[$area->getAreaId()]);
        $this->areas = [];
    }


    /**
     * @internal
     */
    public function dropTables()/*:void*/
    {
        self::dic()->database()->dropTable(Area::TABLE_NAME, false);
    }


    /**
     * @return Factory
     */
    public function factory() : Factory
    {
        return Factory::getInstance();
    }


    /**
     * @param int $area_id
     *
     * @return Area|null
     */
    public function getAreaById(int $area_id)/*: ?Area*/
    {
        if ($this->areas_by_id[$area_id] === null) {
            $this->areas_by_id[$area_id] = Area::where(["area_id" => $area_id])->first();
        }

        return $this->areas_by_id[$area_id];
    }


    /**
     * @param bool $check_visible
     *
     * @return Area[]
     */
    public function getAreas(bool $check_visible = false) : array
    {
        $cache_key = intval($check_visible);

        if ($this->areas[$cache_key] === null) {
            if ($check_visible) {
                $this->areas[$cache_key] = array_values(array_filter($this->getAreas(), function (Area $area) : bool {
                    return $area->isVisible();
                }));
            } else {
                $this->areas[$cache_key] = array_values(Area::get());
            }
        }

        return $this->areas[$cache_key];
    }


    /**
     * @internal
     */
    public function installTables()/*:void*/
    {
        Area::updateDB();
    }


    /**
     * @param Area $area
     */
    public function storeArea(Area $area)/*: void*/
    {
        $area->store();

        $this->areas_by_id[$area->getAreaId()] = $area;
        $this->areas = [];
    }
}
