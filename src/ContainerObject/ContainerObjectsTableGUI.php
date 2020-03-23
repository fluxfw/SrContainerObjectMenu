<?php

namespace srag\Plugins\SrContainerObjectMenu\ContainerObject;

use ilSrContainerObjectMenuPlugin;
use srag\CustomInputGUIs\SrContainerObjectMenu\PropertyFormGUI\Items\Items;
use srag\CustomInputGUIs\SrContainerObjectMenu\TableGUI\TableGUI;
use srag\Plugins\SrContainerObjectMenu\Utils\SrContainerObjectMenuTrait;

/**
 * Class ContainerObjectsTableGUI
 *
 * @package srag\Plugins\SrContainerObjectMenu\ContainerObject
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ContainerObjectsTableGUI extends TableGUI
{

    use SrContainerObjectMenuTrait;
    const PLUGIN_CLASS_NAME = ilSrContainerObjectMenuPlugin::class;
    const LANG_MODULE = ContainerObjectsGUI::LANG_MODULE;


    /**
     * ContainerObjectsTableGUI constructor
     *
     * @param ContainerObjectsGUI $parent
     * @param string              $parent_cmd
     */
    public function __construct(ContainerObjectsGUI $parent, string $parent_cmd)
    {
        parent::__construct($parent, $parent_cmd);
    }


    /**
     * @inheritDoc
     *
     * @param ContainerObject $container_object
     */
    protected function getColumnValue(/*string*/ $column, /*ContainerObject*/ $container_object, /*int*/ $format = self::DEFAULT_FORMAT) : string
    {
        switch ($column) {
            case "object_title":
                $column = htmlspecialchars($container_object->getObject()->getTitle());
                break;

            default:
                $column = htmlspecialchars(Items::getter($container_object, $column));
                break;
        }

        return strval($column);
    }


    /**
     * @inheritDoc
     */
    public function getSelectableColumns2() : array
    {
        $columns = [
            "object_title" => [
                "id"      => "object_title",
                "default" => true,
                "sort"    => false,
                "txt"     => $this->txt("container_object")
            ]
        ];

        return $columns;
    }


    /**
     * @inheritDoc
     */
    protected function initColumns()/*: void*/
    {
        parent::initColumns();

        $this->addColumn($this->txt("actions"));
    }


    /**
     * @inheritDoc
     */
    protected function initCommands()/*: void*/
    {
        self::dic()->toolbar()->addComponent(self::dic()->ui()->factory()->button()->standard($this->txt("add_container_object"), self::dic()->ctrl()
            ->getLinkTargetByClass(ContainerObjectGUI::class, ContainerObjectGUI::CMD_ADD_CONTAINER_OBJECT)));
    }


    /**
     * @inheritDoc
     */
    protected function initData()/*: void*/
    {
        $this->setExternalSegmentation(true);
        $this->setExternalSorting(true);

        $this->setData(self::srContainerObjectMenu()->containerObjects()->getContainerObjects());
    }


    /**
     * @inheritDoc
     */
    protected function initFilterFields()/*: void*/
    {
        $this->filter_fields = [];
    }


    /**
     * @inheritDoc
     */
    protected function initId()/*: void*/
    {
        $this->setId(ilSrContainerObjectMenuPlugin::PLUGIN_ID . "_container_objects");
    }


    /**
     * @inheritDoc
     */
    protected function initTitle()/*: void*/
    {
        $this->setTitle($this->txt("container_objects"));
    }


    /**
     * @param ContainerObject $container_object
     */
    protected function fillRow(/*ContainerObject*/ $container_object)/*: void*/
    {
        self::dic()->ctrl()->setParameterByClass(ContainerObjectGUI::class, ContainerObjectGUI::GET_PARAM_CONTAINER_OBJECT_ID, $container_object->getContainerObjectId());

        parent::fillRow($container_object);

        $this->tpl->setVariable("COLUMN", self::output()->getHTML(self::dic()->ui()->factory()->dropdown()->standard([
            self::dic()->ui()->factory()->link()->standard($this->txt("edit_container_object"), self::dic()->ctrl()
                ->getLinkTargetByClass(ContainerObjectGUI::class, ContainerObjectGUI::CMD_EDIT_CONTAINER_OBJECT)),
            self::dic()->ui()->factory()->link()->standard($this->txt("remove_container_object"), self::dic()->ctrl()
                ->getLinkTargetByClass(ContainerObjectGUI::class, ContainerObjectGUI::CMD_REMOVE_CONTAINER_OBJECT_CONFIRM))
        ])->withLabel($this->txt("actions"))));
    }
}
