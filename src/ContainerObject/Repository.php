<?php

namespace srag\Plugins\SrContainerObjectMenu\ContainerObject;

use ilSrContainerObjectMenuPlugin;
use srag\DIC\SrContainerObjectMenu\DICTrait;
use srag\Plugins\SrContainerObjectMenu\Area\Area;
use srag\Plugins\SrContainerObjectMenu\Utils\SrContainerObjectMenuTrait;

/**
 * Class Repository
 *
 * @package srag\Plugins\SrContainerObjectMenu\ContainerObject
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
     * @var ContainerObject[][]
     */
    protected $container_objects = [];
    /**
     * @var ContainerObject[]
     */
    protected $container_objects_by_id = [];
    /**
     * @var string[]
     */
    protected $menu_identifiers = [];


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
     * @param ContainerObject $container_object
     */
    public function deleteContainerObject(ContainerObject $container_object)/* : void*/
    {
        $container_object->delete();

        foreach (self::srContainerObjectMenu()->areas()->getAreas() as $area) {
            if ($area->getLinkContainerObjectId() === $container_object->getContainerObjectId()) {
                $area->setLinkContainerObjectId();

                self::srContainerObjectMenu()->areas()->storeArea($area);
            }
        }

        self::srContainerObjectMenu()->menu()->deleteMenuItems($container_object->getMenuIdentifier());

        unset($this->container_objects_by_id[$container_object->getContainerObjectId()]);
        $this->container_objects = [];
    }


    /**
     * @internal
     */
    public function dropTables()/* : void*/
    {
        self::dic()->database()->dropTable(ContainerObject::TABLE_NAME, false);
    }


    /**
     * @return Factory
     */
    public function factory() : Factory
    {
        return Factory::getInstance();
    }


    /**
     * @param ContainerObject $container_object
     *
     * @return Area|null
     */
    public function getArea(ContainerObject $container_object)/* : ?Area*/
    {
        if (empty($container_object->getContainerObjectId())) {
            return null;
        }

        if ($this->areas[$container_object->getContainerObjectId()] === null) {
            $areas = $container_object->getAreas();

            if (!empty($areas)) {
                $selected_area = self::srContainerObjectMenu()->selectedArea()->getSelectedArea(self::dic()->user()->getId());

                if ($selected_area->getArea() !== null) {
                    $areas = array_filter($areas, function (Area $area) use ($selected_area) : bool {
                        return ($area->getAreaId() === $selected_area->getAreaId());
                    });
                }

                $this->areas[$container_object->getContainerObjectId()] = current($areas);
            } else {
                $this->areas[$container_object->getContainerObjectId()] = false;
            }
        }

        return ($this->areas[$container_object->getContainerObjectId()] ?: null);
    }


    /**
     * @param int $container_object_id
     *
     * @return ContainerObject|null
     */
    public function getContainerObjectById(int $container_object_id)/* : ?ContainerObject*/
    {
        if ($this->container_objects_by_id[$container_object_id] === null) {
            $this->container_objects_by_id[$container_object_id] = ContainerObject::where(["container_object_id" => $container_object_id])->first();
        }

        return $this->container_objects_by_id[$container_object_id];
    }


    /**
     * @param int|null $area_id
     * @param bool     $check_visible
     * @param bool     $allow_no_areas
     *
     * @return ContainerObject[]
     */
    public function getContainerObjects(/*?*/ int $area_id = null, bool $check_visible = false, bool $allow_no_areas = true) : array
    {
        $cache_key = intval($area_id) . "_" . intval($check_visible) . "_" . intval($allow_no_areas);

        if ($this->container_objects[$cache_key] === null) {
            if ($check_visible) {
                $this->container_objects[$cache_key] = array_values(array_filter($this->getContainerObjects($area_id, false, $allow_no_areas), function (ContainerObject $container_object) : bool {
                    return $container_object->isVisible();
                }));
            } else {
                if (!empty($area_id)) {
                    $this->container_objects[$cache_key] = array_values(array_filter($this->getContainerObjects(null, $check_visible, $allow_no_areas),
                        function (ContainerObject $container_object) use ($area_id, $allow_no_areas) : bool {
                            return $this->isSelectedArea($container_object, $area_id, $allow_no_areas);
                        }));
                } else {
                    $this->container_objects[$cache_key] = array_values(ContainerObject::get());

                    foreach ($this->container_objects[$cache_key] as $container_object) {
                        $this->container_objects_by_id[$container_object->getContainerObjectId()] = $container_object;
                    }
                }
            }
        }

        return $this->container_objects[$cache_key];
    }


    /**
     * @param int|null $container_object_id
     * @param int|null $child_obj_ref_id
     * @param int|null $position
     *
     * @return string
     */
    public function getMenuIdentifier(/*?*/ int $container_object_id = null,/*?*/ int $child_obj_ref_id = null,/*?*/ int $position = null) : string
    {
        $cache_key = intval($container_object_id) . "_" . intval($child_obj_ref_id) . "_" . intval($position);

        if ($this->menu_identifiers[$cache_key] === null) {
            $parts = [
                ilSrContainerObjectMenuPlugin::PLUGIN_ID
            ];

            if (!empty($container_object_id)) {
                $parts[] = $container_object_id;

                if (!empty($child_obj_ref_id)) {
                    $parts[] = $child_obj_ref_id;

                    if (!empty($position)) {
                        $parts[] = $position;
                    }
                }
            }

            $this->menu_identifiers[$cache_key] = implode("_", $parts);
        }

        return $this->menu_identifiers[$cache_key];
    }


    /**
     * @internal
     */
    public function installTables()/* : void*/
    {
        ContainerObject::updateDB();
    }


    /**
     * @param ContainerObject $container_object
     * @param int             $area_id
     * @param bool            $allow_no_areas
     *
     * @return bool
     */
    public function isSelectedArea(ContainerObject $container_object, int $area_id, bool $allow_no_areas = true) : bool
    {
        if (empty($container_object->getAreaIds())) {
            return $allow_no_areas;
        }

        return $container_object->hasAreaId($area_id);
    }


    /**
     * @param ContainerObject $container_object
     */
    public function storeContainerObject(ContainerObject $container_object)/* : void*/
    {
        $container_object->store();

        $this->container_objects_by_id[$container_object->getContainerObjectId()] = $container_object;
        $this->container_objects = [];
        unset($this->areas[$container_object->getContainerObjectId()]);
    }
}
