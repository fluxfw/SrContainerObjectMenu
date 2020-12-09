<?php

namespace srag\Plugins\SrContainerObjectMenu\ContainerObject\Table;

use ilSrContainerObjectMenuPlugin;
use srag\DataTableUI\SrContainerObjectMenu\Component\Column\Column;
use srag\DataTableUI\SrContainerObjectMenu\Component\Data\Row\RowData;
use srag\DataTableUI\SrContainerObjectMenu\Component\Format\Format;
use srag\DataTableUI\SrContainerObjectMenu\Component\Table;
use srag\DataTableUI\SrContainerObjectMenu\Implementation\Column\Formatter\DefaultFormatter;
use srag\DataTableUI\SrContainerObjectMenu\Implementation\Utils\AbstractTableBuilder;
use srag\Plugins\SrContainerObjectMenu\Area\AreasCtrl;
use srag\Plugins\SrContainerObjectMenu\ContainerObject\ContainerObjectCtrl;
use srag\Plugins\SrContainerObjectMenu\ContainerObject\ContainerObjectsCtrl;
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
     * @param ContainerObjectsCtrl $parent
     */
    public function __construct(ContainerObjectsCtrl $parent)
    {
        parent::__construct($parent);
    }


    /**
     * @inheritDoc
     */
    public function render() : string
    {
        self::dic()->toolbar()->addComponent(self::dic()->ui()->factory()->button()->standard(self::plugin()->translate("add_container_object", ContainerObjectsCtrl::LANG_MODULE),
            self::dic()->ctrl()->getLinkTargetByClass(ContainerObjectCtrl::class, ContainerObjectCtrl::CMD_ADD_CONTAINER_OBJECT, "", false, false)));

        return parent::render();
    }


    /**
     * @inheritDoc
     */
    protected function buildTable() : Table
    {
        $table = self::dataTableUI()->table(ilSrContainerObjectMenuPlugin::PLUGIN_ID . "_cont_objs",
            self::dic()->ctrl()->getLinkTarget($this->parent, ContainerObjectsCtrl::CMD_LIST_CONTAINER_OBJECTS, "", false, false),
            self::plugin()->translate("container_objects", ContainerObjectsCtrl::LANG_MODULE), [
                self::dataTableUI()->column()->column("object_title",
                    self::plugin()->translate("container_object", ContainerObjectsCtrl::LANG_MODULE))->withSortable(false),
                self::dataTableUI()->column()->column("menu_title",
                    self::plugin()->translate("menu_title", ContainerObjectsCtrl::LANG_MODULE))->withSortable(false),
                self::dataTableUI()->column()->column("areas_title",
                    self::plugin()->translate("areas", AreasCtrl::LANG_MODULE))->withSortable(false)->withFormatter(new class() extends DefaultFormatter {

                    /**
                     * @inheritDoc
                     */
                    public function formatRowCell(Format $format, $value, Column $column, RowData $row, string $table_id) : string
                    {
                        return strval($value);
                    }
                }),
                self::dataTableUI()->column()->column("actions",
                    self::plugin()->translate("actions", ContainerObjectsCtrl::LANG_MODULE))->withFormatter(self::dataTableUI()->column()->formatter()->actions()->actionsDropdown())
            ], new DataFetcher())->withPlugin(self::plugin());

        return $table;
    }
}
