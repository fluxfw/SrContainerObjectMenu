<?php

namespace srag\Plugins\SrContainerObjectMenu\ContainerObject\Table;

use ilSrContainerObjectMenuPlugin;
use srag\DataTableUI\SrContainerObjectMenu\Component\Data\Data;
use srag\DataTableUI\SrContainerObjectMenu\Component\Data\Row\RowData;
use srag\DataTableUI\SrContainerObjectMenu\Component\Settings\Settings;
use srag\DataTableUI\SrContainerObjectMenu\Implementation\Data\Fetcher\AbstractDataFetcher;
use srag\Plugins\SrContainerObjectMenu\ContainerObject\ContainerObject;
use srag\Plugins\SrContainerObjectMenu\Utils\SrContainerObjectMenuTrait;

/**
 * Class DataFetcher
 *
 * @package srag\Plugins\SrContainerObjectMenu\ContainerObject\Table
 */
class DataFetcher extends AbstractDataFetcher
{

    use SrContainerObjectMenuTrait;

    const PLUGIN_CLASS_NAME = ilSrContainerObjectMenuPlugin::class;


    /**
     * @inheritDoc
     */
    public function fetchData(Settings $settings) : Data
    {
        $data = self::srContainerObjectMenu()->containerObjects()->getContainerObjects();

        $max_count = count($data);

        $data = array_slice($data, $settings->getOffset(), $settings->getRowsCount());

        return self::dataTableUI()->data()->data(array_map(function (ContainerObject $container_object
        ) : RowData {
            return self::dataTableUI()->data()->row()->getter($container_object->getContainerObjectId(), $container_object);
        }, $data), $max_count);
    }
}
