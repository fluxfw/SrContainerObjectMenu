<?php

namespace srag\Plugins\SrContainerObjectMenu\ContainerObject\Table;

use ilSrContainerObjectMenuPlugin;
use srag\DataTableUI\SrContainerObjectMenu\Component\Table;
use srag\DataTableUI\SrContainerObjectMenu\Implementation\Utils\AbstractTableBuilder;
use srag\Plugins\SrContainerObjectMenu\ContainerObject\ContainerObjectGUI;
use srag\Plugins\SrContainerObjectMenu\ContainerObject\ContainerObjectsGUI;
use srag\Plugins\SrContainerObjectMenu\Utils\SrContainerObjectMenuTrait;

/**
 * Class TableBuilder
 *
 * @package srag\Plugins\SrContainerObjectMenu\ContainerObject\Table
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class TableBuilder extends AbstractTableBuilder
{

    use SrContainerObjectMenuTrait;
    const PLUGIN_CLASS_NAME = ilSrContainerObjectMenuPlugin::class;


    /**
     * @inheritDoc
     *
     * @param ContainerObjectsGUI $parent
     */
    public function __construct(ContainerObjectsGUI $parent)
    {
        parent::__construct($parent);
    }


    /**
     * @inheritDoc
     */
    protected function buildTable() : Table
    {
        $table = self::dataTableUI()->table(ilSrContainerObjectMenuPlugin::PLUGIN_ID . "_cont_objs",
            self::dic()->ctrl()->getLinkTarget($this->parent, ContainerObjectsGUI::CMD_LIST_CONTAINER_OBJECTS, "", false, false),
            self::plugin()->translate("container_objects", ContainerObjectsGUI::LANG_MODULE), [
                self::dataTableUI()->column()->column("object_title",
                    self::plugin()->translate("container_object", ContainerObjectsGUI::LANG_MODULE))->withSortable(false)->withFormatter(self::dataTableUI()
                    ->column()
                    ->formatter()
                    ->chainGetter(["object", "title"])),
                self::dataTableUI()->column()->column("menu_title",
                    self::plugin()->translate("menu_title", ContainerObjectsGUI::LANG_MODULE))->withSortable(false),
                self::dataTableUI()->column()->column("actions",
                    self::plugin()->translate("actions", ContainerObjectsGUI::LANG_MODULE))->withFormatter(self::dataTableUI()->column()->formatter()->actions()->actionsDropdown())
            ], new DataFetcher())->withPlugin(self::plugin());

        return $table;
    }


    /**
     * @inheritDoc
     */
    public function render() : string
    {
        self::dic()->toolbar()->addComponent(self::dic()->ui()->factory()->button()->standard(self::plugin()->translate("add_container_object", ContainerObjectsGUI::LANG_MODULE),
            self::dic()->ctrl()->getLinkTargetByClass(ContainerObjectGUI::class, ContainerObjectGUI::CMD_ADD_CONTAINER_OBJECT, "", false, false)));

        return parent::render();
    }
}
