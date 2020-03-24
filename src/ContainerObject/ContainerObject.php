<?php

namespace srag\Plugins\SrContainerObjectMenu\ContainerObject;

use ActiveRecord;
use arConnector;
use ilContainer;
use ilMMItemFacadeInterface;
use ilObjectFactory;
use ilSrContainerObjectMenuPlugin;
use srag\DIC\SrContainerObjectMenu\DICTrait;
use srag\Plugins\SrContainerObjectMenu\Utils\SrContainerObjectMenuTrait;

/**
 * Class ContainerObject
 *
 * @package srag\Plugins\SrContainerObjectMenu\ContainerObject
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ContainerObject extends ActiveRecord
{

    use DICTrait;
    use SrContainerObjectMenuTrait;
    const TABLE_NAME = ilSrContainerObjectMenuPlugin::PLUGIN_ID . "_obj";
    const PLUGIN_CLASS_NAME = ilSrContainerObjectMenuPlugin::class;


    /**
     * @inheritDoc
     */
    public function getConnectorContainerName() : string
    {
        return self::TABLE_NAME;
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
    public function __construct(/*int*/ $primary_key_value = 0, arConnector $connector = null)
    {
        parent::__construct($primary_key_value, $connector);
    }


    /**
     * @return array
     */
    public function getChildren() : array
    {
        return self::srContainerObjectMenu()->containerObjects()->getChildren($this->obj_ref_id);
    }


    /**
     * @param int|null $child_obj_ref_id
     *
     * @return string
     */
    public function getMenuIdentifier(/*?*/ int $child_obj_ref_id = null) : string
    {
        return ilSrContainerObjectMenuPlugin::PLUGIN_ID . (!empty($this->container_object_id) ? "_" . $this->container_object_id . (!empty($child_obj_ref_id) ? "_" . $child_obj_ref_id : "") : "");
    }


    /**
     * @param int|null $child_obj_ref_id
     *
     * @return ilMMItemFacadeInterface|null
     */
    public function getMenuItem(/*?*/ int $child_obj_ref_id = null)/* : ?ilMMItemFacadeInterface*/
    {
        return self::srContainerObjectMenu()->containerObjects()->getMenuItem($this->getMenuIdentifier($child_obj_ref_id));
    }


    /**
     * @return ilContainer
     */
    public function getObject() : ilContainer
    {
        return ilObjectFactory::getInstanceByRefId($this->obj_ref_id, false);
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
    public function setContainerObjectId(int $container_object_id)/*: void*/
    {
        $this->container_object_id = $container_object_id;
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
    public function setObjRefId(int $obj_ref_id)/* : void*/
    {
        $this->obj_ref_id = $obj_ref_id;
    }
}
