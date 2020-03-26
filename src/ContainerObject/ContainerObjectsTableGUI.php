<?php

namespace srag\Plugins\SrContainerObjectMenu\ContainerObject;

use ilSrContainerObjectMenuPlugin;
use srag\DataTable\SrContainerObjectMenu\Component\Data\Data;
use srag\DataTable\SrContainerObjectMenu\Component\Data\Row\RowData;
use srag\DataTable\SrContainerObjectMenu\Component\Settings\Settings;
use srag\DataTable\SrContainerObjectMenu\Implementation\Column\Formatter\Actions\AbstractActionsFormatter;
use srag\DataTable\SrContainerObjectMenu\Implementation\Data\Fetcher\AbstractDataFetcher;
use srag\DIC\SrContainerObjectMenu\DICTrait;
use srag\Plugins\SrContainerObjectMenu\Utils\SrContainerObjectMenuTrait;

/**
 * Class ContainerObjectsTableGUI
 *
 * @package srag\Plugins\SrContainerObjectMenu\ContainerObject
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ContainerObjectsTableGUI
{

    use DICTrait;
    use SrContainerObjectMenuTrait;
    const PLUGIN_CLASS_NAME = ilSrContainerObjectMenuPlugin::class;


    /**
     * ContainerObjectsTableGUI constructor
     */
    public function __construct()
    {

    }


    /**
     * @return string
     */
    public function render() : string
    {
        self::dic()->toolbar()->addComponent(self::dic()->ui()->factory()->button()->standard(self::plugin()->translate("add_container_object", ContainerObjectsGUI::LANG_MODULE), self::dic()->ctrl()
            ->getLinkTargetByClass(ContainerObjectGUI::class, ContainerObjectGUI::CMD_ADD_CONTAINER_OBJECT, "", "", false, false)));

        $table = self::srContainerObjectMenu()->dataTable()->table(ilSrContainerObjectMenuPlugin::PLUGIN_ID . "_cont_objs",
            self::dic()->ctrl()->getLinkTargetByClass(ContainerObjectsGUI::class, ContainerObjectsGUI::CMD_LIST_CONTAINER_OBJECTS),
            self::plugin()->translate("container_objects", ContainerObjectsGUI::LANG_MODULE), [
                self::srContainerObjectMenu()->dataTable()->column()->column("object_title",
                    self::plugin()->translate("container_object", ContainerObjectsGUI::LANG_MODULE))
                    ->withSortable(false)
                    ->withFormatter(self::srContainerObjectMenu()->dataTable()->column()->formatter()->chainGetter(["object", "title"])),
                self::srContainerObjectMenu()->dataTable()->column()->column("actions",
                    self::plugin()->translate("actions", ContainerObjectsGUI::LANG_MODULE))->withFormatter(new class() extends AbstractActionsFormatter {

                    use SrContainerObjectMenuTrait;
                    const PLUGIN_CLASS_NAME = ilSrContainerObjectMenuPlugin::class;


                    /**
                     * @inheritDoc
                     */
                    protected function getActions(RowData $row) : array
                    {
                        self::dic()->ctrl()->setParameterByClass(ContainerObjectGUI::class, ContainerObjectGUI::GET_PARAM_CONTAINER_OBJECT_ID, $row->getRowId());

                        return [
                            self::dic()->ui()->factory()->link()->standard(self::plugin()->translate("edit_container_object", ContainerObjectsGUI::LANG_MODULE), self::dic()->ctrl()
                                ->getLinkTargetByClass(ContainerObjectGUI::class, ContainerObjectGUI::CMD_EDIT_CONTAINER_OBJECT)),
                            self::dic()->ui()->factory()->link()->standard(self::plugin()->translate("remove_container_object", ContainerObjectsGUI::LANG_MODULE), self::dic()->ctrl()
                                ->getLinkTargetByClass(ContainerObjectGUI::class, ContainerObjectGUI::CMD_REMOVE_CONTAINER_OBJECT_CONFIRM))
                        ];
                    }
                })
            ],
            new class() extends AbstractDataFetcher {

                use SrContainerObjectMenuTrait;
                const PLUGIN_CLASS_NAME = ilSrContainerObjectMenuPlugin::class;


                /**
                 * @inheritDoc
                 */
                public function fetchData(Settings $settings) : Data
                {
                    $data = self::srContainerObjectMenu()->containerObjects()->getContainerObjects();

                    return self::srContainerObjectMenu()->dataTable()->data()->data(array_map(function (ContainerObject $container_object
                    ) : RowData {
                        return self::srContainerObjectMenu()->dataTable()->data()->row()->getter($container_object->getContainerObjectId(), $container_object);
                    }, $data), count($data));
                }
            })->withPlugin(self::plugin());

        return self::output()->getHTML($table);
    }
}
