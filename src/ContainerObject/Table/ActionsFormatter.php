<?php

namespace srag\Plugins\SrContainerObjectMenu\ContainerObject\Table;

use ilSrContainerObjectMenuPlugin;
use srag\DataTableUI\SrContainerObjectMenu\Component\Data\Row\RowData;
use srag\DataTableUI\SrContainerObjectMenu\Implementation\Column\Formatter\Actions\AbstractActionsFormatter;
use srag\Plugins\SrContainerObjectMenu\ContainerObject\ContainerObjectGUI;
use srag\Plugins\SrContainerObjectMenu\ContainerObject\ContainerObjectsGUI;
use srag\Plugins\SrContainerObjectMenu\Utils\SrContainerObjectMenuTrait;

/**
 * Class ActionsFormatter
 *
 * @package srag\Plugins\SrContainerObjectMenu\ContainerObject\Table
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ActionsFormatter extends AbstractActionsFormatter
{

    use SrContainerObjectMenuTrait;
    const PLUGIN_CLASS_NAME = ilSrContainerObjectMenuPlugin::class;


    /**
     * @inheritDoc
     */
    protected function getActions(RowData $row) : array
    {
        self::dic()->ctrl()->setParameterByClass(ContainerObjectGUI::class, ContainerObjectGUI::GET_PARAM_CONTAINER_OBJECT_ID, $row->getRowId());

        return [
            self::dic()->ui()->factory()->link()->standard(self::plugin()->translate("edit_container_object", ContainerObjectsGUI::LANG_MODULE),
                self::dic()->ctrl()->getLinkTargetByClass(ContainerObjectGUI::class, ContainerObjectGUI::CMD_EDIT_CONTAINER_OBJECT, "", false, false)),
            self::dic()->ui()->factory()->link()->standard(self::plugin()->translate("remove_container_object", ContainerObjectsGUI::LANG_MODULE),
                self::dic()->ctrl()->getLinkTargetByClass(ContainerObjectGUI::class, ContainerObjectGUI::CMD_REMOVE_CONTAINER_OBJECT_CONFIRM, "", false, false))
        ];
    }
}
