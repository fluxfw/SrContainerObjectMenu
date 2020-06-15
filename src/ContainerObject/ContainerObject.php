<?php

namespace srag\Plugins\SrContainerObjectMenu\ContainerObject;

use ActiveRecord;
use arConnector;
use ilContainer;
use ILIAS\UI\Component\Component;
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

    const PLUGIN_CLASS_NAME = ilSrContainerObjectMenuPlugin::class;
    const TABLE_NAME = ilSrContainerObjectMenuPlugin::PLUGIN_ID . "_obj";
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
     * @var ilContainer|null
     */
    protected $object = null;


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
     * @inheritDoc
     *
     * @deprecated
     */
    public static function returnDbTableName() : string
    {
        return self::TABLE_NAME;
    }


    /**
     * @return Component[]
     */
    public function getActions() : array
    {
        self::dic()->ctrl()->setParameterByClass(ContainerObjectGUI::class, ContainerObjectGUI::GET_PARAM_CONTAINER_OBJECT_ID, $this->container_object_id);

        return [
            self::dic()->ui()->factory()->link()->standard(self::plugin()->translate("edit_container_object", ContainerObjectsGUI::LANG_MODULE),
                self::dic()->ctrl()->getLinkTargetByClass(ContainerObjectGUI::class, ContainerObjectGUI::CMD_EDIT_CONTAINER_OBJECT, "", false, false)),
            self::dic()->ui()->factory()->link()->standard(self::plugin()->translate("remove_container_object", ContainerObjectsGUI::LANG_MODULE),
                self::dic()->ctrl()->getLinkTargetByClass(ContainerObjectGUI::class, ContainerObjectGUI::CMD_REMOVE_CONTAINER_OBJECT_CONFIRM, "", false, false))
        ];
    }


    /**
     * @return array
     */
    public function getChildren() : array
    {
        return self::srContainerObjectMenu()->containerObjects()->getChildren($this->getObject());
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
    public function setContainerObjectId(int $container_object_id)/*: void*/
    {
        $this->container_object_id = $container_object_id;
    }


    /**
     * @param int|null $child_obj_ref_id
     * @param int|null $position
     *
     * @return string
     */
    public function getMenuIdentifier(/*?*/ int $child_obj_ref_id = null,/*?*/ int $position = null) : string
    {
        $parts = [
            ilSrContainerObjectMenuPlugin::PLUGIN_ID
        ];

        if (!empty($this->container_object_id)) {
            $parts[] = $this->container_object_id;

            if (!empty($child_obj_ref_id)) {
                $parts[] = $child_obj_ref_id;

                if (!empty($position)) {
                    $parts[] = $position;
                }
            }
        }

        return implode("_", $parts);
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
     * @return string
     */
    public function getMenuTitle() : string
    {
        return (($menu_item = $this->getMenuItem()) !== null ? $menu_item->getDefaultTitle() : "");
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


    /**
     * @return ilContainer
     */
    public function getObject() : ilContainer
    {
        if ($this->object === null) {
            $this->object = ilObjectFactory::getInstanceByRefId($this->obj_ref_id, false);
        }

        return $this->object;
    }
}
