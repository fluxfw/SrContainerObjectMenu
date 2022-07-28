<?php

namespace srag\Plugins\SrContainerObjectMenu\Object;

use ilContainer;
use ilContainerSorting;
use ilObject;
use ilObjectFactory;
use ilSrContainerObjectMenuPlugin;
use srag\DIC\SrContainerObjectMenu\DICTrait;
use srag\Plugins\SrContainerObjectMenu\Utils\SrContainerObjectMenuTrait;

/**
 * Class Repository
 *
 * @package srag\Plugins\SrContainerObjectMenu\Object
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
     * @var string[][]
     */
    protected $children = [];
    /**
     * @var bool[]
     */
    protected $has_read_access = [];
    /**
     * @var ilObject[]
     */
    protected $object_by_ref_id = [];


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

    }


    /**
     * @return Factory
     */
    public function factory() : Factory
    {
        return Factory::getInstance();
    }


    /**
     * @param int $obj_ref_id
     *
     * @return string[]
     */
    public function getChildren(int $obj_ref_id) : array
    {
        if ($this->children[$obj_ref_id] === null) {
            $this->children[$obj_ref_id] = [];

            $object = $this->getObjectByRefId($obj_ref_id);

            if ($object !== null && $object instanceof ilContainer) {
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
                        $this->children[$obj_ref_id][$sub_item["child"]] = $sub_item["title"];
                    }
                }
            }
        }

        return $this->children[$obj_ref_id];
    }


    /**
     * @param int $obj_ref_id
     *
     * @return ilObject|null
     */
    public function getObjectByRefId(int $obj_ref_id) : ?ilObject
    {
        if ($this->object_by_ref_id[$obj_ref_id] === null) {
            $this->object_by_ref_id[$obj_ref_id] = ilObjectFactory::getInstanceByRefId($obj_ref_id, false);
        }

        return ($this->object_by_ref_id[$obj_ref_id] ?: null);
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
    public function installTables() : void
    {

    }
}
