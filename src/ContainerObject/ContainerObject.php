<?php

namespace srag\Plugins\SrContainerObjectMenu\ContainerObject;

use ActiveRecord;
use arConnector;
use ILIAS\UI\Component\Component;
use ilLink;
use ilObject;
use ilSrContainerObjectMenuPlugin;
use srag\DIC\SrContainerObjectMenu\DICTrait;
use srag\Plugins\SrContainerObjectMenu\Area\Area;
use srag\Plugins\SrContainerObjectMenu\Utils\SrContainerObjectMenuTrait;

/**
 * Class ContainerObject
 *
 * @package srag\Plugins\SrContainerObjectMenu\ContainerObject
 */
class ContainerObject extends ActiveRecord
{

    use DICTrait;
    use SrContainerObjectMenuTrait;

    const PLUGIN_CLASS_NAME = ilSrContainerObjectMenuPlugin::class;
    const TABLE_NAME = ilSrContainerObjectMenuPlugin::PLUGIN_ID . "_obj";
    /**
     * @var int[]
     *
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_is_notnull   true
     */
    protected $area_ids = [];
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     * @con_is_primary   true
     * @con_sequence     true
     */
    protected $container_object_id;
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     */
    protected $obj_ref_id = 0;


    /**
     * ContainerObject constructor
     *
     * @param int              $primary_key_value
     * @param arConnector|null $connector
     */
    public function __construct(/*int*/ $primary_key_value = 0, /*?*/ arConnector $connector = null)
    {
        parent::__construct($primary_key_value, $connector);
    }


    /**
     * @inheritDoc
     *
     * @deprecated
     */
    public static function returnDbTableName() : string
    {
        return self::TABLE_NAME;
    }


    /**
     * @param int $area_id
     */
    public function addAreaId(int $area_id) : void
    {
        if (!$this->hasAreaId($area_id)) {
            $this->area_ids[] = $area_id;
        }
    }


    /**
     * @return Component[]
     */
    public function getActions() : array
    {
        self::dic()->ctrl()->setParameterByClass(ContainerObjectCtrl::class, ContainerObjectCtrl::GET_PARAM_CONTAINER_OBJECT_ID, $this->container_object_id);

        return [
            self::dic()->ui()->factory()->link()->standard(self::plugin()->translate("edit_container_object", ContainerObjectsCtrl::LANG_MODULE),
                self::dic()->ctrl()->getLinkTargetByClass(ContainerObjectCtrl::class, ContainerObjectCtrl::CMD_EDIT_CONTAINER_OBJECT, "", false, false)),
            self::dic()->ui()->factory()->link()->standard(self::plugin()->translate("remove_container_object", ContainerObjectsCtrl::LANG_MODULE),
                self::dic()->ctrl()->getLinkTargetByClass(ContainerObjectCtrl::class, ContainerObjectCtrl::CMD_REMOVE_CONTAINER_OBJECT_CONFIRM, "", false, false))
        ];
    }


    /**
     * @return Area|null
     */
    public function getArea() : ?Area
    {
        return self::srContainerObjectMenu()->containerObjects()->getArea($this);
    }


    /**
     * @return string
     */
    public function getAreaColorHex() : string
    {
        return (($area = $this->getArea()) !== null ? $area->getColorHex() : "");
    }


    /**
     * @return int[]
     */
    public function getAreaIds() : array
    {
        return $this->area_ids;
    }


    /**
     * @param int[] $area_ids
     */
    public function setAreaIds(array $area_ids) : void
    {
        $this->area_ids = array_map("intval", array_values($area_ids));
    }


    /**
     * @param int|null $position
     *
     * @return string
     */
    public function getAreaMenuIdentifier(?int $position = null) : string
    {
        return (($area = $this->getArea()) !== null ? $area->getMenuIdentifier($position) : "");
    }


    /**
     * @return Area[]
     */
    public function getAreas() : array
    {
        return array_map(function (int $area_id) : Area {
            return self::srContainerObjectMenu()->areas()->getAreaById($area_id);
        }, $this->area_ids);
    }


    /**
     * @return string
     */
    public function getAreasTitle() : string
    {
        return nl2br(implode("\n", array_map(function (Area $area) : string {
            return $area->getTitle2();
        }, $this->getAreas())), false);
    }


    /**
     * @return string[]
     */
    public function getChildren() : array
    {
        return self::srContainerObjectMenu()->objects()->getChildren($this->obj_ref_id);
    }


    /**
     * @inheritDoc
     */
    public function getConnectorContainerName() : string
    {
        return self::TABLE_NAME;
    }


    /**
     * @return int
     */
    public function getContainerObjectId() : int
    {
        return $this->container_object_id;
    }


    /**
     * @param int $container_object_id
     */
    public function setContainerObjectId(int $container_object_id) : void
    {
        $this->container_object_id = $container_object_id;
    }


    /**
     * @param int|null $child_obj_ref_id
     *
     * @return string
     */
    public function getLink(?int $child_obj_ref_id = null) : string
    {
        return ilLink::_getStaticLink(!empty($child_obj_ref_id) ? $child_obj_ref_id : $this->obj_ref_id);
    }


    /**
     * @param int|null $child_obj_ref_id
     * @param int|null $position
     *
     * @return string
     */
    public function getMenuCSSIdentifier(?int $child_obj_ref_id = null, ?int $position = null) : string
    {
        return self::srContainerObjectMenu()->menu()->getMenuCSSIdentifier($this->getMenuIdentifier($child_obj_ref_id, $position));
    }


    /**
     * @param int|null $child_obj_ref_id
     * @param int|null $position
     *
     * @return string
     */
    public function getMenuIdentifier(?int $child_obj_ref_id = null, ?int $position = null) : string
    {
        return self::srContainerObjectMenu()->containerObjects()->getMenuIdentifier($this->container_object_id, $child_obj_ref_id, $position);
    }


    /**
     * @return string
     */
    public function getMenuTitle() : string
    {
        if (empty($menu_title = self::srContainerObjectMenu()->menu()->getMenuTitle($this->getMenuIdentifier()))) {
            $menu_title = $this->getObjectTitle();
        }

        return $menu_title;
    }


    /**
     * @return int
     */
    public function getObjRefId() : int
    {
        return $this->obj_ref_id;
    }


    /**
     * @param int $obj_ref_id
     */
    public function setObjRefId(int $obj_ref_id) : void
    {
        $this->obj_ref_id = $obj_ref_id;
    }


    /**
     * @return ilObject|null
     */
    public function getObject() : ?ilObject
    {
        return self::srContainerObjectMenu()->objects()->getObjectByRefId($this->obj_ref_id);
    }


    /**
     * @return string
     */
    public function getObjectTitle() : string
    {
        return (($object = $this->getObject()) !== null ? $object->getTitle() : "");
    }


    /**
     * @return string
     */
    public function getTitle() : string
    {
        $title = $this->getObjectTitle();

        if (!empty($menu_title = $this->getMenuTitle()) && $menu_title !== $title) {
            $title .= " (" . $menu_title . ")";
        }

        return $title;
    }


    /**
     * @param int $area_id
     *
     * @return bool
     */
    public function hasAreaId(int $area_id) : bool
    {
        return in_array($area_id, $this->area_ids);
    }


    /**
     * @param int|null $obj_ref_id
     * @param bool     $check_visible
     *
     * @return bool
     */
    public function isVisible(?int $obj_ref_id = null, bool $check_visible = false) : bool
    {
        return (($check_visible ? self::srContainerObjectMenu()
                ->containerObjects()
                ->isSelectedArea($this, self::srContainerObjectMenu()->selectedArea()->getSelectedArea(self::dic()->user()->getId())->getAreaId()) : true)
            && self::srContainerObjectMenu()->objects()->hasReadAccess(!empty($obj_ref_id) ? $obj_ref_id : $this->obj_ref_id));
    }


    /**
     * @param int $area_id
     */
    public function removeAreaId(int $area_id) : void
    {
        if ($this->hasAreaId($area_id)) {
            $this->setAreaIds(array_filter($this->area_ids, function (int $area_id_) use ($area_id) : bool {
                return ($area_id_ !== $area_id);
            }));
        }
    }


    /**
     * @inheritDoc
     */
    public function sleep(/*string*/ $field_name)
    {
        $field_value = $this->{$field_name};

        switch ($field_name) {
            case "area_ids":
                return json_encode($field_value);

            default:
                return parent::sleep($field_name);
        }
    }


    /**
     * @inheritDoc
     */
    public function wakeUp(/*string*/ $field_name, $field_value)
    {
        switch ($field_name) {
            case "area_ids":
                return (array) json_decode($field_value, true);

            case "container_object_id":
            case "obj_ref_id":
                return intval($field_value);

            default:
                return parent::wakeUp($field_name, $field_value);
        }
    }
}
