<?php

namespace srag\DataTable\SrContainerObjectMenu\Component\Data;

use srag\DataTable\SrContainerObjectMenu\Component\Data\Fetcher\Factory as FetcherFactory;
use srag\DataTable\SrContainerObjectMenu\Component\Data\Row\Factory as RowFactory;
use srag\DataTable\SrContainerObjectMenu\Component\Data\Row\RowData;

/**
 * Interface Factory
 *
 * @package srag\DataTable\SrContainerObjectMenu\Component\Data
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
interface Factory
{

    /**
     * @param RowData[] $data
     * @param int       $max_count
     *
     * @return Data
     */
    public function data(array $data, int $max_count) : Data;


    /**
     * @return FetcherFactory
     */
    public function fetcher() : FetcherFactory;


    /**
     * @return RowFactory
     */
    public function row() : RowFactory;
}
