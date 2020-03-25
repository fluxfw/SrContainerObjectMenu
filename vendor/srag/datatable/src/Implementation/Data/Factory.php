<?php

namespace srag\DataTable\SrContainerObjectMenu\Implementation\Data;

use srag\DataTable\SrContainerObjectMenu\Component\Data\Data as DataInterface;
use srag\DataTable\SrContainerObjectMenu\Component\Data\Factory as FactoryInterface;
use srag\DataTable\SrContainerObjectMenu\Component\Data\Fetcher\Factory as FetcherFactoryInterface;
use srag\DataTable\SrContainerObjectMenu\Component\Data\Row\Factory as RowFactoryInterface;
use srag\DataTable\SrContainerObjectMenu\Implementation\Data\Fetcher\Factory as FetcherFactory;
use srag\DataTable\SrContainerObjectMenu\Implementation\Data\Row\Factory as RowFactory;
use srag\DataTable\SrContainerObjectMenu\Utils\DataTableTrait;
use srag\DIC\SrContainerObjectMenu\DICTrait;

/**
 * Class Factory
 *
 * @package srag\DataTable\SrContainerObjectMenu\Implementation\Data
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class Factory implements FactoryInterface
{

    use DICTrait;
    use DataTableTrait;
    /**
     * @var self|null
     */
    protected static $instance = null;


    /**
     * @return self
     */
    public static function getInstance() : self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * Factory constructor
     */
    private function __construct()
    {

    }


    /**
     * @inheritDoc
     */
    public function data(array $data, int $max_count) : DataInterface
    {
        return new Data($data, $max_count);
    }


    /**
     * @inheritDoc
     */
    public function fetcher() : FetcherFactoryInterface
    {
        return FetcherFactory::getInstance();
    }


    /**
     * @inheritDoc
     */
    public function row() : RowFactoryInterface
    {
        return RowFactory::getInstance();
    }
}
