<?php

namespace srag\Plugins\SrContainerObjectMenu\Config;

use ActiveRecord;
use arConnector;
use ilSrContainerObjectMenuPlugin;
use srag\CustomInputGUIs\SrContainerObjectMenu\TabsInputGUI\MultilangualTabsInputGUI;
use srag\DIC\SrContainerObjectMenu\DICTrait;
use srag\Plugins\SrContainerObjectMenu\Utils\SrContainerObjectMenuTrait;

/**
 * Class Config
 *
 * @package srag\Plugins\SrContainerObjectMenu\Config
 */
class Config extends ActiveRecord
{

    use DICTrait;
    use SrContainerObjectMenuTrait;

    const PLUGIN_CLASS_NAME = ilSrContainerObjectMenuPlugin::class;
    const TABLE_NAME = ilSrContainerObjectMenuPlugin::PLUGIN_ID . "_config";
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
    protected $config_id;
    /**
     * @var array
     *
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_is_notnull   true
     */
    protected $selected_area_menu_titles = [];


    /**
     * Config constructor
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
     * @return int
     */
    public function getConfigId() : int
    {
        return $this->config_id;
    }


    /**
     * @param int $config_id
     */
    public function setConfigId(int $config_id) : void
    {
        $this->config_id = $config_id;
    }


    /**
     * @inheritDoc
     */
    public function getConnectorContainerName() : string
    {
        return self::TABLE_NAME;
    }


    /**
     * @param string|null $lang_key
     * @param bool        $use_default_if_not_set
     *
     * @return string
     */
    public function getSelectedAreaMenuTitle(?string $lang_key = null, bool $use_default_if_not_set = true) : string
    {
        return strval(MultilangualTabsInputGUI::getValueForLang($this->selected_area_menu_titles, $lang_key, "selected_area_menu_title", $use_default_if_not_set));
    }


    /**
     * @return array
     */
    public function getSelectedAreaMenuTitles() : array
    {
        return $this->selected_area_menu_titles;
    }


    /**
     * @param array $selected_area_menu_titles
     */
    public function setSelectedAreaMenuTitles(array $selected_area_menu_titles) : void
    {
        $this->selected_area_menu_titles = $selected_area_menu_titles;
    }


    /**
     * @param string $selected_area_menu_title
     * @param string $lang_key
     */
    public function setSelectedAreaMenuTitle(string $selected_area_menu_title, string $lang_key) : void
    {
        MultilangualTabsInputGUI::setValueForLang($this->selected_area_menu_titles, $selected_area_menu_title, $lang_key, "selected_area_menu_title");
    }


    /**
     * @inheritDoc
     */
    public function sleep(/*string*/ $field_name)
    {
        $field_value = $this->{$field_name};

        switch ($field_name) {
            case "selected_area_menu_titles":
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
            case "config_id":
                return intval($field_value);

            case "selected_area_menu_titles":
                return (array) json_decode($field_value, true);

            default:
                return parent::wakeUp($field_name, $field_value);
        }
    }
}
