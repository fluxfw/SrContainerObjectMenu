<?php

namespace srag\Plugins\SrContainerObjectMenu\Area;

use ActiveRecord;
use arConnector;
use ILIAS\UI\Component\Component;
use ilSrContainerObjectMenuPlugin;
use srag\CustomInputGUIs\SrContainerObjectMenu\TabsInputGUI\MultilangualTabsInputGUI;
use srag\DIC\SrContainerObjectMenu\DICTrait;
use srag\Plugins\SrContainerObjectMenu\ContainerObject\ContainerObject;
use srag\Plugins\SrContainerObjectMenu\Utils\SrContainerObjectMenuTrait;

/**
 * Class Area
 *
 * @package srag\Plugins\SrContainerObjectMenu\Area
 */
class Area extends ActiveRecord
{

    use DICTrait;
    use SrContainerObjectMenuTrait;

    const PLUGIN_CLASS_NAME = ilSrContainerObjectMenuPlugin::class;
    const TABLE_NAME = ilSrContainerObjectMenuPlugin::PLUGIN_ID . "_area";
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
    protected $area_id;
    /**
     * @var string
     *
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_is_notnull   true
     */
    protected $color = "";
    /**
     * @var int|null
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   false
     */
    protected $link_container_object_id = null;
    /**
     * @var array
     *
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_is_notnull   true
     */
    protected $titles = [];


    /**
     * Area constructor
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
     * @param int|null $position
     *
     * @return int
     */
    public function calcPosition(?int $position = null) : int
    {
        return self::srContainerObjectMenu()->areas()->calcPosition($this, $position);
    }


    /**
     * @return Component[]
     */
    public function getActions() : array
    {
        self::dic()->ctrl()->setParameterByClass(AreaCtrl::class, AreaCtrl::GET_PARAM_AREA_ID, $this->area_id);

        return [
            self::dic()->ui()->factory()->link()->standard(self::plugin()->translate("edit_area", AreasCtrl::LANG_MODULE),
                self::dic()->ctrl()->getLinkTargetByClass(AreaCtrl::class, AreaCtrl::CMD_EDIT_AREA, "", false, false)),
            self::dic()->ui()->factory()->link()->standard(self::plugin()->translate("remove_area", AreasCtrl::LANG_MODULE),
                self::dic()->ctrl()->getLinkTargetByClass(AreaCtrl::class, AreaCtrl::CMD_REMOVE_AREA_CONFIRM, "", false, false))
        ];
    }


    /**
     * @return int
     */
    public function getAreaId() : int
    {
        return $this->area_id;
    }


    /**
     * @param int $area_id
     */
    public function setAreaId(int $area_id) : void
    {
        $this->area_id = $area_id;
    }


    /**
     * @return string
     */
    public function getColor() : string
    {
        return $this->color;
    }


    /**
     * @param string $color
     */
    public function setColor(string $color) : void
    {
        $this->color = $color;
    }


    /**
     * @return string
     */
    public function getColorHex() : string
    {
        if (!empty($this->color)) {
            return "#" . $this->color;
        } else {
            return "";
        }
    }


    /**
     * @inheritDoc
     */
    public function getConnectorContainerName() : string
    {
        return self::TABLE_NAME;
    }


    /**
     * @param bool $check_visible
     *
     * @return ContainerObject[]
     */
    public function getContainerObjects(bool $check_visible = false) : array
    {
        if (empty($this->area_id)) {
            return [];
        }

        return self::srContainerObjectMenu()->containerObjects()->getContainerObjects($this->area_id, $check_visible, false);
    }


    /**
     * @return int[]
     */
    public function getContainerObjectsIds() : array
    {
        return array_map(function (ContainerObject $container_object) : int {
            return $container_object->getContainerObjectId();
        }, $this->getContainerObjects());
    }


    /**
     * @return string
     */
    public function getContainerObjectsTitle() : string
    {
        return nl2br(implode("\n", array_map(function (ContainerObject $container_object) : string {
            return $container_object->getTitle();
        }, $this->getContainerObjects())), false);
    }


    /**
     * @return array
     */
    public function getCssVariables() : array
    {
        $variables = [];

        if (!empty($this->getColorHex())) {
            $variables["color"] = $this->getColorHex();
        }
        if (!empty($this->getLinkContainerObjectLink())) {
            $variables["link"] = base64_encode($this->getLinkContainerObjectLink());
        }
        if (!empty($this->getTitle())) {
            $variables["title"] = "\"" . str_replace("\"", "", $this->getTitle()) . "\"";
        }

        return $variables;
    }


    /**
     * @return ContainerObject|null
     */
    public function getLinkContainerObject() : ?ContainerObject
    {
        if (!empty($this->link_container_object_id)) {
            return self::srContainerObjectMenu()->containerObjects()->getContainerObjectById($this->link_container_object_id);
        } else {
            return null;
        }
    }


    /**
     * @return int|null
     */
    public function getLinkContainerObjectId() : ?int
    {
        return $this->link_container_object_id;
    }


    /**
     * @param int|null $link_container_object_id
     */
    public function setLinkContainerObjectId(?int $link_container_object_id = null) : void
    {
        $this->link_container_object_id = $link_container_object_id;
    }


    /**
     * @return string
     */
    public function getLinkContainerObjectLink() : string
    {
        if ($this->getLinkContainerObject() !== null) {
            return $this->getLinkContainerObject()->getLink();
        } else {
            return "";
        }
    }


    /**
     * @return string
     */
    public function getLinkContainerObjectTitle() : string
    {
        if ($this->getLinkContainerObject() !== null) {
            return $this->getLinkContainerObject()->getTitle();
        } else {
            return "";
        }
    }


    /**
     * @param int|null $position
     *
     * @return string
     */
    public function getMenuCSSIdentifier(?int $position = null) : string
    {
        return self::srContainerObjectMenu()->menu()->getMenuCSSIdentifier($this->getMenuIdentifier($position));
    }


    /**
     * @param int|null $position
     *
     * @return string
     */
    public function getMenuIdentifier(?int $position = null) : string
    {
        return self::srContainerObjectMenu()->areas()->getMenuIdentifier($this->area_id, $position);
    }


    /**
     * @return string
     */
    public function getMenuTitle() : string
    {
        if (empty($menu_title = self::srContainerObjectMenu()->menu()->getMenuTitle($this->getMenuIdentifier($this->calcPosition())))) {
            $menu_title = $this->getTitle();
        }

        return $menu_title;
    }


    /**
     * @param string|null $lang_key
     * @param bool        $use_default_if_not_set
     *
     * @return string
     */
    public function getTitle(?string $lang_key = null, bool $use_default_if_not_set = true) : string
    {
        return strval(MultilangualTabsInputGUI::getValueForLang($this->titles, $lang_key, "title", $use_default_if_not_set));
    }


    /**
     * @return string
     */
    public function getTitle2() : string
    {
        $title = $this->getTitle();

        if (!empty($menu_title = $this->getMenuTitle()) && $menu_title !== $title) {
            $title .= " (" . $menu_title . ")";
        }

        return $title;
    }


    /**
     * @return array
     */
    public function getTitles() : array
    {
        return $this->titles;
    }


    /**
     * @param array $titles
     */
    public function setTitles(array $titles) : void
    {
        $this->titles = $titles;
    }


    /**
     * @return bool
     */
    public function isVisible() : bool
    {
        return (!empty($this->getContainerObjects(true)));
    }


    /**
     * @param int[] $container_object_ids
     */
    public function setContainerObjectsIds(array $container_object_ids) : void
    {
        if (empty($this->area_id)) {
            return;
        }

        $store_container_objects = [];

        foreach ($container_object_ids as $container_object_id) {
            $container_object = self::srContainerObjectMenu()->containerObjects()->getContainerObjectById($container_object_id);

            if ($container_object->hasAreaId($this->area_id)) {
                continue;
            }

            $container_object->addAreaId($this->area_id);

            $store_container_objects[$container_object->getContainerObjectId()] = $container_object;
        }

        foreach ($this->getContainerObjects() as $container_object) {
            if (in_array($container_object->getContainerObjectId(), $container_object_ids)) {
                continue;
            }

            $container_object->removeAreaId($this->area_id);

            $store_container_objects[$container_object->getContainerObjectId()] = $container_object;
        }

        foreach ($store_container_objects as $container_object) {
            self::srContainerObjectMenu()->containerObjects()->storeContainerObject($container_object);
        }
    }


    /**
     * @param string $title
     * @param string $lang_key
     */
    public function setTitle(string $title, string $lang_key) : void
    {
        MultilangualTabsInputGUI::setValueForLang($this->titles, $title, $lang_key, "title");
    }


    /**
     * @inheritDoc
     */
    public function sleep(/*string*/ $field_name)
    {
        $field_value = $this->{$field_name};

        switch ($field_name) {
            case "titles":
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
            case "area_id":
                return intval($field_value);

            case "titles":
                return (array) json_decode($field_value, true);

            default:
                return parent::wakeUp($field_name, $field_value);
        }
    }
}
