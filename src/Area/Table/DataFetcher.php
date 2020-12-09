<?php

namespace srag\Plugins\SrContainerObjectMenu\Area\Table;

use ilSrContainerObjectMenuPlugin;
use srag\DataTableUI\SrContainerObjectMenu\Component\Data\Data;
use srag\DataTableUI\SrContainerObjectMenu\Component\Data\Row\RowData;
use srag\DataTableUI\SrContainerObjectMenu\Component\Settings\Settings;
use srag\DataTableUI\SrContainerObjectMenu\Implementation\Data\Fetcher\AbstractDataFetcher;
use srag\Plugins\SrContainerObjectMenu\Area\Area;
use srag\Plugins\SrContainerObjectMenu\Utils\SrContainerObjectMenuTrait;

/**
 * Class DataFetcher
 *
 * @package srag\Plugins\SrContainerObjectMenu\Area\Table
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
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
        $data = self::srContainerObjectMenu()->areas()->getAreas();

        return self::dataTableUI()->data()->data(array_map(function (Area $area) : RowData {
            return self::dataTableUI()->data()->row()->getter($area->getAreaId(), $area);
        }, $data), count($data));
    }
}
