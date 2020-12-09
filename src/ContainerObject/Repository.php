<?php

namespace srag\Plugins\SrContainerObjectMenu\ContainerObject;

use ilContainer;
use ilContainerSorting;
use ilObjectFactory;
use ilSrContainerObjectMenuPlugin;
use srag\DIC\SrContainerObjectMenu\DICTrait;
use srag\Plugins\SrContainerObjectMenu\Utils\SrContainerObjectMenuTrait;

/**
 * Class Repository
 *
 * @package srag\Plugins\SrContainerObjectMenu\ContainerObject
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
     * @var array
     */
    protected $children = [];
    /**
     * @var ContainerObject[]
     */
    protected $container_objects = [];
    /**
     * @var ContainerObject[]
     */
    protected $container_objects_by_id = [];
    /**
     * @var array
     */
    protected $has_read_access = [];


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
    public function deleteContainerObject(ContainerObject $container_object)/*: void*/
    {
        $container_object->delete();

        self::srContainerObjectMenu()->menu()->deleteMenuItems($container_object->getMenuIdentifier());

        unset($this->container_objects_by_id[$container_object->getContainerObjectId()]);
        $this->container_objects = [];
    }


    /**
     * @internal
     */
    public function dropTables()/*:void*/
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
     * @param ilContainer|int $object
     *
     * @return array
     */
    public function getChildren(/*ilContainer*/ $object) : array
    {
        if ($object instanceof ilContainer) {
            $cache_key = $object->getRefId();
        } else {
            if (is_int($object)) {
                $cache_key = $object;
            } else {
                return [];
            }
        }

        if ($this->children[$cache_key] === null) {
            $this->children[$cache_key] = [];

            if (is_int($object)) {
                $object = ilObjectFactory::getInstanceByRefId($object, false);
            }

            if ($object instanceof ilContainer) {
                $types = ilContainerSorting::_getInstance($object->getId())->getBlockPositions();
                if (empty($types)) {
                    $types = array_reduce(self::dic()
                        ->objDefinition()
                        ->getGroupedRepositoryObjectTypes($object->getType()), function (array $types, array $type) : array {
                        $types = array_merge($types, $type["objs"]);

                        return $types;
                    }, []);
                }

                $sub_items = $object->getSubItems();

                foreach ($types as $type) {
                    foreach ((array) $sub_items[$type] as $sub_item) {
                        $this->children[$cache_key][$sub_item["child"]] = $sub_item["title"];
                    }
                }
            }
        }

        return $this->children[$cache_key];
    }


    /**
     * @param int $container_object_id
     *
     * @return ContainerObject|null
     */
    public function getContainerObjectById(int $container_object_id)/*: ?ContainerObject*/
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
                        function (ContainerObject $container_object) use ($area_id, $allow_no_areas): bool {
                            return $this->isSelectedArea($container_object, $area_id, $allow_no_areas);
                        }));
                } else {
                    $this->container_objects[$cache_key] = array_values(ContainerObject::get());
                }
            }
        }

        return $this->container_objects[$cache_key];
    }


    /**
     * @param int $obj_ref_id
     *
     * @return bool
     */
    public function hasReadAccess(int $obj_ref_id) : bool
    {
        if ($this->has_read_access[$obj_ref_id] === null) {
            $this->has_read_access[$obj_ref_id] = self::dic()->access()->checkAccess("read", "", $obj_ref_id);
        }

        return $this->has_read_access[$obj_ref_id];
    }


    /**
     * @internal
     */
    public function installTables()/*:void*/
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

        return in_array($area_id, $container_object->getAreaIds());
    }


    /**
     * @param ContainerObject $container_object
     */
    public function storeContainerObject(ContainerObject $container_object)/*: void*/
    {
        $container_object->store();

        $this->container_objects_by_id[$container_object->getContainerObjectId()] = $container_object;
        $this->container_objects = [];
    }
}
