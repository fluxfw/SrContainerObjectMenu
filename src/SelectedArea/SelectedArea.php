<?php

namespace srag\Plugins\SrContainerObjectMenu\SelectedArea;

use ActiveRecord;
use arConnector;
use ilSrContainerObjectMenuPlugin;
use srag\DIC\SrContainerObjectMenu\DICTrait;
use srag\Plugins\SrContainerObjectMenu\Area\Area;
use srag\Plugins\SrContainerObjectMenu\Area\AreasCtrl;
use srag\Plugins\SrContainerObjectMenu\Utils\SrContainerObjectMenuTrait;

/**
 * Class SelectedArea
 *
 * @package srag\Plugins\SrContainerObjectMenu\SelectedArea
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class SelectedArea extends ActiveRecord
{

    use DICTrait;
    use SrContainerObjectMenuTrait;

    const NO_AREA_ID = 0;
    const PLUGIN_CLASS_NAME = ilSrContainerObjectMenuPlugin::class;
    const TABLE_NAME = ilSrContainerObjectMenuPlugin::PLUGIN_ID . "_sel_area";
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     */
    protected $area_id = self::NO_AREA_ID;
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
    protected $select_area_id;
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     */
    protected $usr_id = 0;


    /**
     * SelectedArea constructor
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
     * @return Area|null
     */
    public function getArea()/* : ?Area*/
    {
        $areas = self::srContainerObjectMenu()->areas()->getAreas(true);

        foreach ($areas as $area) {
            if ($area->getAreaId() === $this->area_id) {
                return $area;
            }
        }

        return (reset($areas) ?: null);
    }


    /**
     * @param bool $raw
     *
     * @return int
     */
    public function getAreaId(bool $raw = false) : int
    {
        if ($raw) {
            return $this->area_id;
        } else {
            return (($area = $this->getArea()) !== null ? $area->getAreaId() : self::NO_AREA_ID);
        }
    }


    /**
     * @param int $area_id
     */
    public function setAreaId(int $area_id = self::NO_AREA_ID)/* : void*/
    {
        $this->area_id = $area_id;
    }


    /**
     * @return string
     */
    public function getAreaTitle() : string
    {
        return (($area = $this->getArea()) !== null ? $area->getTitle() : "");
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
    public function getSelectAreaId() : int
    {
        return $this->select_area_id;
    }


    /**
     * @param int $select_area_id
     */
    public function setSelectAreaId(int $select_area_id)/* : void*/
    {
        $this->select_area_id = $select_area_id;
    }


    /**
     * @return string
     */
    public function getTitle() : string
    {
        $title = self::plugin()->translate("areas", AreasCtrl::LANG_MODULE);

        if (!empty($area_title = $this->getAreaTitle())) {
            $title .= " (" . $area_title . ")";
        }

        return $title;
    }


    /**
     * @return int
     */
    public function getUsrId() : int
    {
        return $this->usr_id;
    }


    /**
     * @param int $usr_id
     */
    public function setUsrId(int $usr_id)/* : void*/
    {
        $this->usr_id = $usr_id;
    }


    /**
     * @inheritDoc
     */
    public function sleep(/*string*/ $field_name)
    {
        $field_value = $this->{$field_name};

        switch ($field_name) {
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
            case "select_area_id":
            case "usr_id":
                return intval($field_value);

            default:
                return parent::wakeUp($field_name, $field_value);
        }
    }
}
