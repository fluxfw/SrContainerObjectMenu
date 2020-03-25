<?php

namespace srag\DataTable\SrContainerObjectMenu\Implementation\Data\Fetcher;

use srag\DataTable\SrContainerObjectMenu\Component\Data\Fetcher\DataFetcher;
use srag\DataTable\SrContainerObjectMenu\Component\Table;
use srag\DataTable\SrContainerObjectMenu\Utils\DataTableTrait;
use srag\DIC\SrContainerObjectMenu\DICTrait;

/**
 * Class AbstractDataFetcher
 *
 * @package srag\DataTable\SrContainerObjectMenu\Implementation\Data\Fetcher
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class AbstractDataFetcher implements DataFetcher
{

    use DICTrait;
    use DataTableTrait;


    /**
     * AbstractDataFetcher constructor
     */
    public function __construct()
    {

    }


    /**
     * @inheritDoc
     */
    public function getNoDataText(Table $component) : string
    {
        return $component->getPlugin()->translate("no_data", Table::LANG_MODULE);
    }


    /**
     * @inheritDoc
     */
    public function isFetchDataNeedsFilterFirstSet() : bool
    {
        return false;
    }
}
