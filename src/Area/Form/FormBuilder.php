<?php

namespace srag\Plugins\SrContainerObjectMenu\Area\Form;

use ilSrContainerObjectMenuPlugin;
use srag\CustomInputGUIs\SrContainerObjectMenu\FormBuilder\AbstractFormBuilder;
use srag\Plugins\SrContainerObjectMenu\Area\Area;
use srag\Plugins\SrContainerObjectMenu\Area\AreaCtrl;
use srag\Plugins\SrContainerObjectMenu\Area\AreasCtrl;
use srag\Plugins\SrContainerObjectMenu\Utils\SrContainerObjectMenuTrait;

/**
 * Class FormBuilder
 *
 * @package  srag\Plugins\SrContainerObjectMenu\Area\Form
 *
 * @author   studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class FormBuilder extends AbstractFormBuilder
{

    use SrContainerObjectMenuTrait;

    const PLUGIN_CLASS_NAME = ilSrContainerObjectMenuPlugin::class;
    /**
     * @var Area
     */
    protected $area;


    /**
     * @inheritDoc
     *
     * @param AreaCtrl $parent
     * @param Area     $area
     */
    public function __construct(AreaCtrl $parent, Area $area)
    {
        $this->area = $area;

        parent::__construct($parent);
    }


    /**
     * @inheritDoc
     */
    protected function getButtons() : array
    {
        $buttons = [];

        if (!empty($this->area->getAreaId())) {
            $buttons[AreaCtrl::CMD_UPDATE_AREA] = self::plugin()->translate("save", AreasCtrl::LANG_MODULE);
        } else {
            $buttons[AreaCtrl::CMD_CREATE_AREA] = self::plugin()->translate("add", AreasCtrl::LANG_MODULE);
            $buttons[AreaCtrl::CMD_BACK] = self::plugin()->translate("cancel", AreasCtrl::LANG_MODULE);
        }

        return $buttons;
    }


    /**
     * @inheritDoc
     */
    protected function getData() : array
    {
        $data = [
            "title" => $this->area->getTitle()
        ];

        return $data;
    }


    /**
     * @inheritDoc
     */
    protected function getFields() : array
    {
        $fields = [
            "title" => self::dic()->ui()->factory()->input()->field()->text(self::plugin()->translate("title", AreasCtrl::LANG_MODULE))->withRequired(true),
        ];

        return $fields;
    }


    /**
     * @inheritDoc
     */
    protected function getTitle() : string
    {
        if (!empty($this->area->getAreaId())) {
            return self::plugin()->translate("edit_area", AreasCtrl::LANG_MODULE);
        } else {
            return self::plugin()->translate("add_area", AreasCtrl::LANG_MODULE);
        }
    }


    /**
     * @inheritDoc
     */
    protected function storeData(array $data)/*:void*/
    {
        $this->area->setTitle(strval($data["title"]));

        self::srContainerObjectMenu()->areas()->storeArea($this->area);
    }
}
